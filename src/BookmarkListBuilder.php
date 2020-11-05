<?php

namespace Drupal\bookmarks;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Bookmark entities.
 *
 * @ingroup bookmarks
 */
class BookmarkListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Bookmark ID');
    $header['bundle_id'] = $this->t('Bundle');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\bookmarks\Entity\Bookmark $entity */
    $row['id'] = $entity->id();
    $row['bundle_id'] = $entity->bundle();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.bookmark.edit_form',
      ['bookmark' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
