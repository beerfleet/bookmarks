<?php

namespace Drupal\bookmarks\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Bookmark revision.
 *
 * @ingroup bookmarks
 */
class BookmarkRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Bookmark revision.
   *
   * @var \Drupal\bookmarks\Entity\BookmarkInterface
   */
  protected $revision;

  /**
   * The Bookmark storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $bookmarkStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->bookmarkStorage = $container->get('entity_type.manager')->getStorage('bookmark');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bookmark_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.bookmark.version_history', ['bookmark' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $bookmark_revision = NULL) {
    $this->revision = $this->BookmarkStorage->loadRevision($bookmark_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->BookmarkStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Bookmark: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Bookmark %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.bookmark.canonical',
       ['bookmark' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {bookmark_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.bookmark.version_history',
         ['bookmark' => $this->revision->id()]
      );
    }
  }

}
