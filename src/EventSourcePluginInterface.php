<?php

namespace Drupal\middlebury_event_sync;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for (loading of) Event Source plugins.
 *
 * @see \Drupal\middlebury_event_sync\EventSourcePluginManager
 * @see plugin_api
 */
interface EventSourcePluginInterface extends PluginInspectionInterface {

  /**
   * Answer an array of Events found at the source.
   *
   * @param \Drupal\middlebury_event_sync\EventSourceInterface $source
   *   The source to fetch from.
   *
   * @return \Drupal\middlebury_event_sync\Event[]
   *   An array of Events found at the source.
   */
  public function getEvents(EventSourceInterface $source);

}
