<?php

namespace Drupal\bookmarks\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Bookmark entities.
 *
 * @ingroup bookmarks
 */
interface BookmarkInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Bookmark name.
   *
   * @return string
   *   Name of the Bookmark.
   */
  public function getName();

  /**
   * Sets the Bookmark name.
   *
   * @param string $name
   *   The Bookmark name.
   *
   * @return \Drupal\bookmarks\Entity\BookmarkInterface
   *   The called Bookmark entity.
   */
  public function setName($name);
  
  /**
   * Gets the assigned tags
   * 
   * @return string
   *   Bookmarks' tags
   */
  public function getTags();

  /**
   * Gets the Bookmark creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Bookmark.
   */
  public function getCreatedTime();

  /**
   * Sets the Bookmark creation timestamp.
   *
   * @param int $timestamp
   *   The Bookmark creation timestamp.
   *
   * @return \Drupal\bookmarks\Entity\BookmarkInterface
   *   The called Bookmark entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Bookmark revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Bookmark revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\bookmarks\Entity\BookmarkInterface
   *   The called Bookmark entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Bookmark revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Bookmark revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\bookmarks\Entity\BookmarkInterface
   *   The called Bookmark entity.
   */
  public function setRevisionUserId($uid);

}
