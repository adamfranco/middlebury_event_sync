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
    $header['ttl'] = $this->t('TTL');
    $header['time_shift'] = $this->t('Time-Shift');
    $header['enabled'] = $this->t('Enabled');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {

    $row['label'] = $this->getLabel($entity);
    $row['id'] = $entity->id();

    $plugin = $entity->getPlugin();
    $row['type'] = $plugin->getLabel();

    $row['ttl'] = $plugin->getTtl();
    $row['time_shift'] = $this->t('@shift hours', ['@shift' => $plugin->getTimeShift()]);
    $row['enabled'] = $plugin->getEnabled()?$this->t('Enabled'):$this->t('Disabled');

    return $row + parent::buildRow($entity);
  }

}
