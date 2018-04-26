<?php

namespace Drupal\middlebury_event_sync;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Provides an interface defining the accessor methods on an event.
 */
class EventBase implements EventInterface {

  /**
   * Source of the event.
   *
   * @var \Drupal\middlebury_event_sync\EventSourceInterface
   *    The event source.
   */
  protected $source;

  /**
   * Set source of the event.
   *
   * @param \Drupal\middlebury_event_sync\EventSourceInterface $source
   *   The event source definition.
   */
  public function setSource(EventSourceInterface $source) {
    $this->source = $source;
  }

  /**
   * Answer source of the event.
   *
   * @return \Drupal\middlebury_event_sync\EventSourceInterface
   *   The event source definition.
   */
  public function getSource() {
    if (empty($this->source)) {
      throw new \Exception('Event sources cannot be empty, no source was set on this event.');
    }
    return $this->source;
  }

  /**
   * Id of the event.
   *
   * @var string
   *    The id.
   */
  protected $id;

  /**
   * Set the id of the event, should be globally unique.
   *
   * @param string $id
   *   The id.
   */
  public function setId($id) {
    if (empty($id)) {
      throw new \InvalidArgumentException('Event ids cannot be empty.');
    }
    $this->id = $id;
  }

  /**
   * Answer the id of the event, should be globally unique.
   *
   * @return string
   *   The id.
   */
  public function getId() {
    if (empty($this->id)) {
      throw new \Exception('Event ids cannot be empty, no id was set on this event.');
    }
    return $this->id;
  }

  /**
   * The title variable.
   *
   * @var string
   *    The title.
   */
  protected $title;

  /**
   * Set the title of the event.
   *
   * @param string $title
   *   The title.
   */
  public function setTitle($title) {
    if (empty($title)) {
      throw new \InvalidArgumentException('Event titles cannot be empty.');
    }
    $this->title = $title;
  }

  /**
   * Answer the title of the event.
   *
   * @return string
   *   The title.
   */
  public function getTitle() {
    if (empty($this->title)) {
      throw new \Exception('Event titles cannot be empty, no title was set on this event.');
    }
    return $this->title;
  }

  /**
   * The start time variable.
   *
   * @var \Drupal\Core\Datetime\DrupalDateTime
   *    The start time.
   */
  protected $startTime;

  /**
   * Answer the start time of the event.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $startTime
   *   The start time.
   */
  public function setStartDateTime(DrupalDateTime $startTime) {
    $this->startTime = $startTime;
  }

  /**
   * Answer the start time of the event.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The start time.
   */
  public function getStartDateTime() {
    if (empty($this->startTime)) {
      throw new \Exception('Event start times cannot be empty, no start time was set on this event.');
    }
    return $this->startTime;
  }

  /**
   * The end time variable.
   *
   * @var \Drupal\Core\Datetime\DrupalDateTime
   *    The end time.
   */
  protected $endTime;

  /**
   * Answer the end time of the event.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $endTime
   *   The end time.
   */
  public function setEndDateTime(DrupalDateTime $endTime) {
    $this->endTime = $endTime;
  }

  /**
   * Answer true if the event has an end time specified.
   *
   * @return bool
   *   True if the event has an end time.
   */
  public function hasEndDateTime() {
    return !empty($this->endTime);
  }

  /**
   * Answer the end time of the event.
   *
   * @return \DateTime
   *   The end time.
   */
  public function getEndDateTime() {
    if (empty($this->endTime)) {
      throw new \Exception('Event does not have an end time.');
    }
    return $this->endTime;
  }

  /**
   * The blurb variable.
   *
   * @var string
   *    The blurb.
   */
  protected $blurb;

  /**
   * Set the blurb for the event.
   *
   * @param string $blurb
   *   The blurb.
   */
  public function setBlurb($blurb) {
    if (!is_string($blurb)) {
      throw new \Exception('Blurb must be a string.');
    }
    $this->blurb = $blurb;
  }

  /**
   * Is a blurb available for this event?
   *
   * @return bool
   *   True if the event has a blurb.
   */
  public function hasBlurb() {
    return isset($this->blurb);
  }

  /**
   * Answer the blurb for the event.
   *
   * @return string
   *   The blurb.
   */
  public function getBlurb() {
    if (!isset($this->blurb)) {
      throw new \Exception('Event does not have a blurb.');
    }
    return $this->blurb;
  }

  /**
   * The body variable.
   *
   * @var string
   *    The body.
   */
  protected $body;

  /**
   * Set the body for the event.
   *
   * @param string $body
   *   The body.
   */
  public function setBody($body) {
    if (!is_string($body)) {
      throw new \Exception('Body must be a string.');
    }
    $this->body = $body;
  }

  /**
   * Is a body available for this event?
   *
   * @return bool
   *   True if the event has a body.
   */
  public function hasBody() {
    return isset($this->body);
  }

  /**
   * Answer the body for the event.
   *
   * @return string
   *   The body.
   */
  public function getBody() {
    if (!isset($this->body)) {
      throw new \Exception('Event does not have a body.');
    }
    return $this->body;
  }

  /**
   * The open to the public variable.
   *
   * @var bool
   *    The open to the public state.
   */
  protected $openToThePublic;

  /**
   * Set the open to the public state for the event.
   *
   * @param bool $openToThePublic
   *   Whether the event is open to the public.
   */
  public function setOpenToThePublic($openToThePublic) {
    if (!is_bool($openToThePublic)) {
      throw new \Exception('Open to the public must be a boolean.');
    }
    $this->openToThePublic = $openToThePublic;
  }

  /**
   * Is an open to the public state known for this event?
   *
   * @return bool
   *   True if the event has a known open to the public state.
   */
  public function hasOpenToThePublic() {
    return isset($this->openToThePublic);
  }

  /**
   * Answer true if the open to the public state for the event exists.
   *
   * @return bool
   *   Whether open to the public state exists.
   */
  public function isOpenToThePublic() {
    if (!isset($this->openToThePublic)) {
      throw new \Exception('Event does not have a open to the public state set.');
    }
    return $this->openToThePublic;
  }

  /**
   * The location label variable.
   *
   * @var string
   *    The location label.
   */
  protected $locationLabel;

  /**
   * Set the location label for the event.
   *
   * @param string $locationLabel
   *   The location label.
   */
  public function setLocationLabel($locationLabel) {
    if (!is_string($locationLabel)) {
      throw new \Exception('The location label must be a string.');
    }
    $this->locationLabel = $locationLabel;
  }

  /**
   * The location id variable.
   *
   * @var string
   *    The id of the location taxonomy term.
   */
  protected $locationId;

  /**
   * Set the location taxonomy term id for the event.
   *
   * @param string $locationId
   *   The id of the location taxonomy term.
   */
  public function setLocationId($locationId) {
    if (empty($locationId) || !is_string($locationId)) {
      throw new \Exception('Location id must be a string.');
    }
    $this->locationId = $locationId;
  }

  /**
   * Is a location id available for this event?
   *
   * @return bool
   *   True if the event has a location id.
   */
  public function hasLocationId() {
    return isset($this->locationId);
  }

  /**
   * Answer the taxonomy term id for the location for the event.
   *
   * @return string
   *   The location id.
   */
  public function getLocationId() {
    if (!isset($this->locationId)) {
      throw new \Exception('Event does not have an location id set.');
    }
    return $this->locationId;
  }

  /**
   * The admission price variable.
   *
   * @var string
   *    The admission price.
   */
  protected $admissionPrice;

  /**
   * Set the admission price for the event.
   *
   * @param string $admissionPrice
   *   The admission price.
   */
  public function setAdmissionPrice($admissionPrice) {
    if (!is_string($admissionPrice)) {
      throw new \Exception('Admission price must be a string.');
    }
    $this->admissionPrice = $admissionPrice;
  }

  /**
   * Is a admission price known for this event?
   *
   * @return bool
   *   True if the event has a known admission price.
   */
  public function hasAdmissionPrice() {
    return isset($this->admissionPrice);
  }

  /**
   * Answer the admission price for the event.
   *
   * @return string
   *   The admission price.
   */
  public function getAdmissionPrice() {
    if (!isset($this->admissionPrice)) {
      throw new \Exception('Event does not have an admission price set.');
    }
    return $this->admissionPrice;
  }

  /**
   * The organizer name variable.
   *
   * @var string
   *    The organizer name.
   */
  protected $organizerName;

  /**
   * Set the event organizer name.
   *
   * @param string $organizerName
   *   The organizer name.
   */
  public function setOrganizerName($organizerName) {
    if (!is_string($organizerName)) {
      throw new \Exception('Organizer name must be a string.');
    }
    $this->organizerName = $organizerName;
  }

  /**
   * Is an organizer name available for this event?
   *
   * @return bool
   *   True if the event has a organizer name.
   */
  public function hasOrganizerName() {
    return isset($this->organizerName);
  }

  /**
   * Answer the organizer name for the event.
   *
   * @return string
   *   The organizer name.
   */
  public function getOrganizerName() {
    if (!isset($this->organizerName)) {
      throw new \Exception('Event does not have an organizer name set.');
    }
    return $this->organizerName;
  }

  /**
   * The organizer email variable.
   *
   * @var string
   *    The organizer email.
   */
  protected $organizerEmail;

  /**
   * Set the organizer email for the event.
   *
   * @param string $organizerEmail
   *   The organizer email.
   */
  public function setOrganizerEmail($organizerEmail) {
    if (!is_string($organizerEmail)) {
      throw new \Exception('Organizer email must be a string.');
    }
    $this->organizerEmail = $organizerEmail;
  }

  /**
   * Is a organizer email available for this event?
   *
   * @return bool
   *   True if the event has a organizer email.
   */
  public function hasOrganizerEmail() {
    return isset($this->organizerEmail);
  }

  /**
   * Answer the organizer email for the event.
   *
   * @return string
   *   The organizer email.
   */
  public function getOrganizerEmail() {
    if (!isset($this->organizerEmail)) {
      throw new \Exception('Event does not have an organizer email set.');
    }
    return $this->organizerEmail;
  }

  /**
   * The organizer telephone variable.
   *
   * @var string
   *    The organizer telephone.
   */
  protected $organizerTelephone;

  /**
   * Set the organizer telephone for the event.
   *
   * @param string $organizerTelephone
   *   The organizer telephone.
   */
  public function setOrganizerTelephone($organizerTelephone) {
    if (!is_string($organizerTelephone)) {
      throw new \Exception('Organizer telephone must be a string.');
    }
    $this->organizerTelephone = $organizerTelephone;
  }

  /**
   * Is a organizer telephone available for this event?
   *
   * @return bool
   *   True if the event has a organizer telephone.
   */
  public function hasOrganizerTelephone() {
    return isset($this->organizerTelephone);
  }

  /**
   * Answer the organizer telephone for the event.
   *
   * @return string
   *   The organizer telephone.
   */
  public function getOrganizerTelephone() {
    if (!isset($this->organizerTelephone)) {
      throw new \Exception('Event does not have an organizer telephone set.');
    }
    return $this->organizerTelephone;
  }

  /**
   * The featured photo url variable.
   *
   * @var string
   *    The URL of a featured photo.
   */
  protected $featuredPhotoUrl;

  /**
   * Set the featured photo URL for the event.
   *
   * @param string $featuredPhotoUrl
   *   The URL of a featured photo.
   */
  public function setFeaturedPhotoUrl($featuredPhotoUrl) {
    if (!is_string($featuredPhotoUrl)) {
      throw new \Exception('Featured photo URL must be a string.');
    }
    $this->featuredPhotoUrl = $featuredPhotoUrl;
  }

  /**
   * Is a featured photo available for this event?
   *
   * @return bool
   *   True if the event has a photo.
   */
  public function hasFeaturedPhotoUrl() {
    return isset($this->featuredPhotoUrl);
  }

  /**
   * Answer the URL of a featured photo for the event.
   *
   * @return string
   *   The photo URL.
   */
  public function getFeaturedPhotoUrl() {
    if (!isset($this->featuredPhotoUrl)) {
      throw new \Exception('Event does not have a featured photo URL set.');
    }
    return $this->featuredPhotoUrl;
  }

  /**
   * The event type variable.
   *
   * @var array
   *    The type tags for an event
   */
  protected $eventTypes;

  /**
   * Set the event type tags for the event.
   *
   * @param array $eventTypes
   *   The type tags for an event.
   */
  public function setEventTypes(array $eventTypes) {
    if (!is_array($eventTypes)) {
      throw new \InvalidArgumentException('Event types must be an array.');
    }
    $this->eventTypes = $eventTypes;
  }

  /**
   * Are event types available for this event?
   *
   * @return bool
   *   True if the event supports event types.
   */
  public function hasEventTypes() {
    return isset($this->eventTypes);
  }

  /**
   * Answer an array of the event type strings for this event.
   *
   * @return array
   *   An array of the event type strings.
   */
  public function getEventTypes() {
    if (!isset($this->eventTypes)) {
      throw new \Exception('Event does not have event types set.');
    }
    return $this->eventTypes;
  }

}
