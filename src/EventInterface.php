<?php

namespace Drupal\middlebury_event_sync;

/**
 * Provides an interface defining the accessor methods on an event.
 */
interface EventInterface {

  /**
   * Answer source of the event.
   *
   * @return \Drupal\middlebury_event_sync\EventSourceInterface
   *   The event source definition.
   */
  public function getSource();

  /**
   * Answer the id of the event, should be globally unique.
   *
   * @return string
   *   The id.
   */
  public function getId();

  /**
   * Answer the Title of the event.
   *
   * @return string
   *   The title.
   */
  public function getTitle();

  /**
   * Answer the start DateTime of the event.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The start date & time.
   */
  public function getStartDateTime();

  /**
   * Answer true if the event has an end-date specified.
   *
   * @return bool
   *   True if the event has an end date & time.
   */
  public function hasEndDateTime();

  /**
   * Answer the end DateTime of the event.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The end date & time.
   */
  public function getEndDateTime();

  /**
   * Is a blurb available for this event?
   *
   * @return bool
   *   True if the event has a blurb.
   */
  public function hasBlurb();

  /**
   * Answer the blurb for the event.
   *
   * @return string
   *   The blurb.
   */
  public function getBlurb();

  /**
   * Is a body available for this event?
   *
   * @return bool
   *   True if the event has a body.
   */
  public function hasBody();

  /**
   * Answer the body for the event.
   *
   * @return string
   *   The body.
   */
  public function getBody();

  /**
   * Is an OpenToThePublic state known for this event?
   *
   * @return bool
   *   True if the event has a known OpenToThePublic state.
   */
  public function hasOpenToThePublic();

  /**
   * Answer true if the OpenToThePublic for the event.
   *
   * @return bool
   *   The OpenToThePublic state.
   */
  public function isOpenToThePublic();

  /**
   * Is a location id available for this event?
   *
   * @return bool
   *   True if the event has a location id.
   */
  public function hasLocationId();

  /**
   * Answer the Location Id for the event.
   *
   * This should be the id of a location entity. Finding and mapping from the
   * source data to this location entity is the responsibility of the plugin.
   *
   * @return int
   *   The LocationId.
   */
  public function getLocationId();

  /**
   * Is a AdmissionPrice known for this event?
   *
   * @return bool
   *   True if the event has a known AdmissionPrice.
   */
  public function hasAdmissionPrice();

  /**
   * Answer the AdmissionPrice for the event.
   *
   * @return string
   *   The AdmissionPrice.
   */
  public function getAdmissionPrice();

  /**
   * Is a OrganizerName available for this event?
   *
   * @return bool
   *   True if the event has a OrganizerName.
   */
  public function hasOrganizerName();

  /**
   * Answer the OrganizerName for the event.
   *
   * @return string
   *   The OrganizerName.
   */
  public function getOrganizerName();

  /**
   * Is a OrganizerEmail available for this event?
   *
   * @return bool
   *   True if the event has a OrganizerEmail.
   */
  public function hasOrganizerEmail();

  /**
   * Answer the OrganizerEmail for the event.
   *
   * @return string
   *   The OrganizerEmail.
   */
  public function getOrganizerEmail();

  /**
   * Is a OrganizerTelephone available for this event?
   *
   * @return bool
   *   True if the event has a OrganizerTelephone.
   */
  public function hasOrganizerTelephone();

  /**
   * Answer the OrganizerTelephone for the event.
   *
   * @return string
   *   The OrganizerTelephone.
   */
  public function getOrganizerTelephone();

  /**
   * Is a featured photo available for this event?
   *
   * @return bool
   *   True if the event has a photo.
   */
  public function hasFeaturedPhotoUrl();

  /**
   * Answer the URL of a featured photo for the event.
   *
   * @return string
   *   The photo URL.
   */
  public function getFeaturedPhotoUrl();

  /**
   * Are event types available for this event?
   *
   * @return bool
   *   True if the event supports event types.
   */
  public function hasEventTypes();

  /**
   * Answer an array of the event type strings for this event.
   *
   * @return array
   *   An array of the event type strings.
   */
  public function getEventTypes();

}
