<?php

namespace Drupal\bookmarks\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Bookmark type entity.
 *
 * @ConfigEntityType(
 *   id = "bookmark_type",
 *   label = @Translation("Bookmark type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\bookmarks\BookmarkTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\bookmarks\Form\BookmarkTypeForm",
 *       "edit" = "Drupal\bookmarks\Form\BookmarkTypeForm",
 *       "delete" = "Drupal\bookmarks\Form\BookmarkTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\bookmarks\BookmarkTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "bookmark_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "bookmark",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/bookmark_type/{bookmark_type}",
 *     "add-form" = "/admin/structure/bookmark_type/add",
 *     "edit-form" = "/admin/structure/bookmark_type/{bookmark_type}/edit",
 *     "delete-form" = "/admin/structure/bookmark_type/{bookmark_type}/delete",
 *     "collection" = "/admin/structure/bookmark_type"
 *   }
 * )
 */
class BookmarkType extends ConfigEntityBundleBase implements BookmarkTypeInterface {

  /**
   * The Bookmark type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Bookmark type label.
   *
   * @var string
   */
  protected $label;

}
