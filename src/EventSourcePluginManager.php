<?php

namespace Drupal\middlebury_event_sync;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides an Event Source Plugin plugin manager.
 *
 * @see \Drupal\middlebury_event_sync\EventSourcePluginInterface
 * @see plugin_api
 */
class EventSourcePluginManager extends DefaultPluginManager {

  /**
   * Constructs a EventSourcePluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/EventSource',
      $namespaces,
      $module_handler,
      'Drupal\middlebury_event_sync\EventSourcePluginInterface',
      'Drupal\middlebury_event_sync\Annotation\EventSource'
    );
    $this->alterInfo('event_source_info');
    $this->setCacheBackend($cache_backend, 'event_source_plugins');
  }

}
