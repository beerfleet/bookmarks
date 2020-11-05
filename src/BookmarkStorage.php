<?php

namespace Drupal\bookmarks;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\bookmarks\Entity\BookmarkInterface;

/**
 * Defines the storage handler class for Bookmark entities.
 *
 * This extends the base storage class, adding required special handling for
 * Bookmark entities.
 *
 * @ingroup bookmarks
 */
class BookmarkStorage extends SqlContentEntityStorage implements BookmarkStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(BookmarkInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {bookmark_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {bookmark_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(BookmarkInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {bookmark_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('bookmark_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
