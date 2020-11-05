<?php

namespace Drupal\bookmarks;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface BookmarkStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Bookmark revision IDs for a specific Bookmark.
   *
   * @param \Drupal\bookmarks\Entity\BookmarkInterface $entity
   *   The Bookmark entity.
   *
   * @return int[]
   *   Bookmark revision IDs (in ascending order).
   */
  public function revisionIds(BookmarkInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Bookmark author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Bookmark revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\bookmarks\Entity\BookmarkInterface $entity
   *   The Bookmark entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(BookmarkInterface $entity);

  /**
   * Unsets the language for all Bookmark with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
