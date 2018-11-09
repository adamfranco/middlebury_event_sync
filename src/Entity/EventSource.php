<?php

namespace Drupal\middlebury_event_sync\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\middlebury_event_sync\EventSourceInterface;
use Drupal\middlebury_event_sync\EventSourcePluginCollection;

/**
 * Defines the EventSource entity.
 *
 * @ConfigEntityType(
 *   id = "event_source",
 *   label = @Translation("Event Source"),
 *   handlers = {
 *     "list_builder" = "Drupal\middlebury_event_sync\Controller\EventSourceListBuilder",
 *     "form" = {
 *       "default" = "Drupal\middlebury_event_sync\Form\EventSourceForm",
 *       "edit" = "Drupal\middlebury_event_sync\Form\EventSourceForm",
 *       "delete" = "Drupal\middlebury_event_sync\Form\EventSourceDeleteForm",
 *     }
 *   },
 *   config_prefix = "event_source",
 *   config_export = {
 *     "id",
 *     "label",
 *     "provider",
 *     "plugin",
 *     "settings",
 *   },
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
class EventSource extends ConfigEntityBase implements EventSourceInterface, EntityWithPluginCollectionInterface {

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
   * The plugin instance settings.
   *
   * @var array
   */
  protected $settings = [];

  /**
   * The plugin collection that holds the event_source plugin for this entity.
   *
   * @var \Drupal\middlebury_event_sync\EventSourcePluginCollection
   */
  protected $pluginCollection;

  /**
   * The plugin instance ID.
   *
   * @var string
   */
  protected $plugin;

  /**
   * {@inheritdoc}
   */
  public function getPlugin() {
    return $this
      ->getPluginCollection()
      ->get($this->plugin);
  }

  /**
   * Encapsulates the creation of the event-source's LazyPluginCollection.
   *
   * @return \Drupal\Component\Plugin\LazyPluginCollection
   *   The event-source's plugin collection.
   */
  protected function getPluginCollection() {
    if (!$this->pluginCollection) {
      $this->pluginCollection = new EventSourcePluginCollection(
        \Drupal::service('plugin.manager.event_source'),
        $this->plugin,
        $this->get('settings'),
        $this
      );
    }
    return $this->pluginCollection;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    return [
      'settings' => $this->getPluginCollection(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginId() {
    return $this->plugin;
  }

}
