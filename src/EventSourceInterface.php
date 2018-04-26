<?php

namespace Drupal\middlebury_event_sync;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining an Example entity.
 */
interface EventSourceInterface extends ConfigEntityInterface {

  /**
   * Sets the type of the Event Source.
   *
   * @param string $type
   *   Type of the Event Source.
   */
  public function setType($type);

  /**
   * Answers the type of the Event Source.
   *
   * @return string
   *   Type of the Event Source
   */
  public function getType();

  /**
   * Sets the URI of the Event Source.
   *
   * @param string $uri
   *   The URI to load data from.
   */
  public function setUri($uri);

  /**
   * Answers the URI of the Event Source.
   *
   * @return string
   *   The URI to load data from.
   */
  public function getUri();

  /**
   * Sets the TTL of the Event Source.
   *
   * @param int $ttl
   *   The min number of seconds between fetches.
   */
  public function setTtl($ttl);

  /**
   * Answers the TTL of the Event Source.
   *
   * @return int
   *   The min number of seconds between fetches.
   */
  public function getTtl();

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

}
