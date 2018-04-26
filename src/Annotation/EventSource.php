<?php

namespace Drupal\middlebury_event_sync\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an EventSource annotation object.
 *
 * Plugin Namespace: Plugin\EventSource.
 *
 * @see \Drupal\middlebury_event_sync\EventSourcePluginInterface
 * @see \Drupal\middlebury_event_sync\EventSourcePluginManager
 * @see hook_event_source_plugin_info_alter()
 * @see plugin_api
 *
 * @Annotation
 */
class EventSource extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the Event Source plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

}
