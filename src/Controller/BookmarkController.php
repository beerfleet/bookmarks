<?php

namespace Drupal\bookmarks\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\bookmarks\Entity\BookmarkInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BookmarkController.
 *
 *  Returns responses for Bookmark routes.
 */
class BookmarkController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Bookmark revision.
   *
   * @param int $bookmark_revision
   *   The Bookmark revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($bookmark_revision) {
    $bookmark = $this->entityTypeManager()->getStorage('bookmark')
      ->loadRevision($bookmark_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('bookmark');

    return $view_builder->view($bookmark);
  }

  /**
   * Page title callback for a Bookmark revision.
   *
   * @param int $bookmark_revision
   *   The Bookmark revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($bookmark_revision) {
    $bookmark = $this->entityTypeManager()->getStorage('bookmark')
      ->loadRevision($bookmark_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $bookmark->label(),
      '%date' => $this->dateFormatter->format($bookmark->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Bookmark.
   *
   * @param \Drupal\bookmarks\Entity\BookmarkInterface $bookmark
   *   A Bookmark object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(BookmarkInterface $bookmark) {
    $account = $this->currentUser();
    $bookmark_storage = $this->entityTypeManager()->getStorage('bookmark');

    $langcode = $bookmark->language()->getId();
    $langname = $bookmark->language()->getName();
    $languages = $bookmark->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $bookmark->label()]) : $this->t('Revisions for %title', ['%title' => $bookmark->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all bookmark revisions") || $account->hasPermission('administer bookmark entities')));
    $delete_permission = (($account->hasPermission("delete all bookmark revisions") || $account->hasPermission('administer bookmark entities')));

    $rows = [];

    $vids = $bookmark_storage->revisionIds($bookmark);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\bookmarks\BookmarkInterface $revision */
      $revision = $bookmark_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $bookmark->getRevisionId()) {
          $link = $this->l($date, new Url('entity.bookmark.revision', [
            'bookmark' => $bookmark->id(),
            'bookmark_revision' => $vid,
          ]));
        }
        else {
          $link = $bookmark->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.bookmark.translation_revert', [
                'bookmark' => $bookmark->id(),
                'bookmark_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.bookmark.revision_revert', [
                'bookmark' => $bookmark->id(),
                'bookmark_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.bookmark.revision_delete', [
                'bookmark' => $bookmark->id(),
                'bookmark_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['bookmark_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
