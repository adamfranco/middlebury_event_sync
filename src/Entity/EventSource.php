<?php

namespace Drupal\middlebury_event_sync\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\middlebury_event_sync\EventSourceInterface;

/**
 * Defines the EventSource entity.
 *
 * @ConfigEntityType(
 *   id = "event_source",
 *   label = @Translation("Event Source"),
 *   handlers = {
 *     "list_builder" = "Drupal\middlebury_event_sync\Controller\EventSourceListBuilder",
 *     "form" = {
 *       "add" = "Drupal\middlebury_event_sync\Form\EventSourceForm",
 *       "edit" = "Drupal\middlebury_event_sync\Form\EventSourceForm",
 *       "delete" = "Drupal\middlebury_event_sync\Form\EventSourceDeleteForm",
 *     }
 *   },
 *   config_prefix = "event_source",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/services/event_source/{event_source}",
 *     "delete-form" = "/admin/config/system/event_source/{event_source}/delete",
 *   }
 * )
 */
class EventSource extends ConfigEntityBase implements EventSourceInterface {

  /**
   * The Event Source ID.
   *
   * @var string
   */
  public $id;

  /**
   * The Event Source label.
   *
   * @var string
   */
  public $label;

  /**
   * The Event Source type.
   *
   * @var string
   */
  protected $type;

  /**
   * The Event Source URI.
   *
   * @var string
   */
  protected $uri;

  /**
   * The Event Source TTL.
   *
   * @var int
   */
  protected $ttl = 3600;

  /**
   * The Event Source time-shift in hours.
   *
   * @var int
   */
  protected $time_shift = 0;

  /**
   * {@inheritdoc}
   */
  public function setType($type) {
    $this->type = $type;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function setUri($uri) {
    $this->uri = $uri;
  }

  /**
   * {@inheritdoc}
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * {@inheritdoc}
   */
  public function setTtl($ttl) {
    $this->ttl = intval($ttl);
  }

  /**
   * {@inheritdoc}
   */
  public function getTtl() {
    return $this->ttl;
  }

  /**
   * {@inheritdoc}
   */
  public function setTimeShift($time_shift) {
    $this->time_shift = intval($time_shift);
  }

  /**
   * {@inheritdoc}
   */
  public function getTimeShift() {
    return $this->time_shift;
  }

}
