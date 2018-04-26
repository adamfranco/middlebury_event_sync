<?php

namespace Drupal\middlebury_event_sync\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Example.
 */
class EventSourceListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Event Source');
    $header['id'] = $this->t('Machine name');
    $header['type'] = $this->t('Type');
    $header['uri'] = $this->t('URI');
    $header['ttl'] = $this->t('TTL');
    $header['time_shift'] = $this->t('Time-Shift');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $type_labels = [
      'r25rss' => t('R25 RSS'),
    ];
    $row['label'] = $this->getLabel($entity);
    $row['id'] = $entity->id();
    if (isset($type_labels[$entity->getType()])) {
      $row['type'] = $type_labels[$entity->getType()];
    }
    else {
      $row['type'] = $entity->getType();
    }
    $row['uri'] = $entity->getUri();
    $row['ttl'] = $entity->getTtl();
    $row['time_shift'] = $this->t('@shift hours', ['@shift' => $entity->getTimeShift()]);

    return $row + parent::buildRow($entity);
  }

}
