<?php

namespace Drupal\middlebury_event_sync;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;

/**
 * Defines an interface for (loading of) Event Source plugins.
 *
 * @see \Drupal\middlebury_event_sync\EventSourcePluginManager
 * @see plugin_api
 */
interface EventSourcePluginInterface extends PluginInspectionInterface, ConfigurablePluginInterface {

  /**
   * Sets the Event Source config instance for this plugin instance.
   *
   * @param \Drupal\middlebury_event_sync\EventSourceInterface $config_instance
   *   The config entity.
   */
  public function setConfigInstance(EventSourceInterface $config_instance);

  /**
   * Answer the Event Source config instance for this plugin instance.
   *
   * @return \Drupal\middlebury_event_sync\EventSourceInterface
   *   The config entity.
   */
  public function getConfigInstance();

  /**
   * Answers the TTL of the Event Source.
   *
   * @return int
   *   The min number of seconds between fetches.
   */
  public function getTtl();

  /**
   * Sets the TTL of the Event Source.
   *
   * @param int $ttl
   *   The min number of seconds between fetches.
   */
  public function setTtl($ttl);

  /**
   * Sets the Time-shift of the Event Source.
   *
   * @param int $time_shift
   *   The number of hours to shift the event time in the feed due to incorrect
   *   timezones in the source.
   */
  public function setTimeShift($time_shift);

  /**
   * Answers the Time-shift of the Event Source.
   *
   * @return int
   *   The number of hours to shift the event time in the feed due to incorrect
   *   timezones in the source.
   */
  public function getTimeShift();

  /**
   * Sets whether or not this source should import events.
   *
   * @param boolean $enable
   *   True if imports should be run.
   */
  public function setEnabled($enabled);

  /**
   * Answers true if this source is enabled for imports
   *
   * @return boolean
   *   True if imports should be run.
   */
  public function getEnabled();

  /**
   * Answer an array of Events found at the source.
   *
   * @return \Drupal\middlebury_event_sync\Event[]
   *   An array of Events found at the source.
   */
  public function getEvents();

}
