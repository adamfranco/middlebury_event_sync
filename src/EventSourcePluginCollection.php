<?php

namespace Drupal\middlebury_event_sync;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Plugin\DefaultSingleLazyPluginCollection;

/**
 * Provides a collection of event-source plugins.
 */
class EventSourcePluginCollection extends DefaultSingleLazyPluginCollection {

  /**
   * The event-source ID this plugin collection belongs to.
   *
   * @var string
   */
  protected $eventSourceId;

  /**
   * Constructs a new EventSourcePluginCollection.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface $manager
   *   The manager to be used for instantiating plugins.
   * @param string $instance_id
   *   The ID of the plugin instance.
   * @param array $configuration
   *   An array of configuration.
   * @param string $event_source_id
   *   The unique ID of the EventSource entity using this plugin.
   */
  public function __construct(PluginManagerInterface $manager, $instance_id, array $configuration, $event_source) {
    $this->eventSource = $event_source;
    parent::__construct($manager, $instance_id, $configuration);
  }

  /**
   * {@inheritdoc}
   */
  protected function initializePlugin($instance_id) {
    if (!$instance_id) {
      throw new PluginException("The event-source '{$this->eventSourceId}' did not specify a plugin.");
    }

    try {
      parent::initializePlugin($instance_id);
      $plugin_instance = $this->pluginInstances[$instance_id];
      if ($plugin_instance instanceof EventSourcePluginInterface) {
        $plugin_instance->setConfigInstance($this->eventSource);
      }
    }
    catch (PluginException $e) {
      $module = $this->configuration['provider'];
      // Ignore event-sources belonging to uninstalled modules, but re-throw
      // valid exceptions when the module is installed and the plugin is
      // misconfigured.
      if (!$module || \Drupal::moduleHandler()->moduleExists($module)) {
        throw $e;
      }
    }
  }

}
