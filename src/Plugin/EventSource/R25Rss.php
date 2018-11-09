<?php

namespace Drupal\middlebury_event_sync\Plugin\EventSource;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\middlebury_event_sync\EventSourcePluginInterface;
use Drupal\middlebury_event_sync\EventBase;
use Drupal\middlebury_event_sync\Plugin\EventSourceBase;

/**
 * Plugin definition for the "R25Rss" plugin.
 *
 * @EventSource(
 *   id = "r25rss",
 *   label = @Translation("R25 RSS"),
 * )
 */
class R25Rss extends EventSourceBase implements EventSourcePluginInterface {

  /**
   * The URI to fetch events from.
   *
   * @var string
   */
  protected $uri;

  /**
   * Set our instance value for URI.
   *
   * @param string $uri
   *   The URI to fetch from.
   */
  public function setUri($uri) {
    $this->uri = $uri;
  }

  /**
   * Answer our instance value for URI.
   *
   * @return string
   *   The URI to fetch from.
   */
  public function getUri() {
    if (empty($this->uri)) {
      return '';
    }
    else {
      return $this->uri;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    parent::setConfiguration($configuration);
    if (isset($configuration['uri'])) {
      $this->setUri($configuration['uri']);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    $configuration = parent::getConfiguration();
    $configuration['uri'] = $this->getUri();
    return $configuration;
  }

  /**
   * Form constructor.
   *
   * Plugin forms are embedded in other forms. In order to know where the plugin
   * form is located in the parent form, #parents and #array_parents must be
   * known, but these are not available during the initial build phase. In order
   * to have these properties available when building the plugin form's
   * elements, let this method return a form element that has a #process
   * callback and build the rest of the form in the callback. By the time the
   * callback is executed, the element's #parents and #array_parents properties
   * will have been set by the form API. For more documentation on #parents and
   * #array_parents, see \Drupal\Core\Render\Element\FormElement.
   *
   * @param array $form
   *   An associative array containing the initial structure of the plugin form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form. Calling code should pass on a subform
   *   state created through
   *   \Drupal\Core\Form\SubformState::createForSubform().
   *
   * @return array
   *   The form structure.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URI'),
      '#maxlength' => 255,
      '#default_value' => $this->getUri(),
      '#description' => $this->t("The URI to source the data from."),
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * Form validation handler.
   *
   * @param array $form
   *   An associative array containing the structure of the plugin form as built
   *   by static::buildConfigurationForm().
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form. Calling code should pass on a subform
   *   state created through
   *   \Drupal\Core\Form\SubformState::createForSubform().
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);
    if (empty($form_state->getValue('uri')) || filter_var($form_state->getValue('uri'), FILTER_VALIDATE_URL) === FALSE) {
      $form_state->setErrorByName('uri', $this->t('The URI must be a valid.'));
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the plugin form as built
   *   by static::buildConfigurationForm().
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form. Calling code should pass on a subform
   *   state created through
   *   \Drupal\Core\Form\SubformState::createForSubform().
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->setUri($form_state->getValue('uri'));
  }

  /**
   * {@inheritdoc}
   */
  public function getEvents() {
    $events = [];
    $start = NULL;
    $page = 0;
    while ($page < 5) {
      $page++;
      $page_events = $this->getEventsByDate($start);
      if (!count($page_events)) {
        // Stop fetching if there are no more events in the feed.
        break;
      }
      $start = $this->getLatestStartDate($page_events);
      $events = array_merge($events, $page_events);
    }

    return $events;
  }

  /**
   * Answer an array of events beginning from a particular date to allow paging.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $start
   *   (optional) A start date from which to fetch.
   *
   * @return array
   *   An array of \Drupal\middlebury_event_sync\EventInterface events.
   */
  protected function getEventsByDate(DrupalDateTime $start = NULL) {
    $client = \Drupal::httpClient();
    $uri = $this->getUri();
    if ($start) {
      $param = 'startdate=' . $start->format('Ymd');
      if (preg_match('/.+\?.+/', $uri)) {
        $uri .= '&' . $param;
      }
      else {
        $uri .= '?' . $param;
      }
    }
    try {
      $response = $client->get($uri);
      // Expected result.
      // getBody() returns an instance of Psr\Http\Message\StreamInterface.
      // @see http://docs.guzzlephp.org/en/latest/psr7.html#body
      $data = simplexml_load_string($response->getBody()->getContents());
      $events = [];
      foreach ($data->channel->item as $item) {
        $event = $this->extract($item);
        $event->setSource($this);
        $events[$event->getId()] = $event;
      }
      return $events;
    }
    catch (RequestException $e) {
      watchdog_exception('middlebury_event_sync', $e);
    }
  }

  /**
   * Answer the latest start date found in an array of events.
   *
   * @param array $events
   *   An array of events to compare.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The latest event start date in the array or NULL if empty.
   */
  protected function getLatestStartDate(array $events) {
    $latest = NULL;
    foreach ($events as $event) {
      if (!$latest || $event->getStartDateTime() > $latest) {
        $latest = $event->getStartDateTime();
      }
    }
    return $latest;
  }

  /**
   * Convert an RSS item into an Event object.
   *
   * @param \SimpleXMLElement $item
   *   The RSS item.
   *
   * @return \Drupal\middlebury_event_sync\EventInterface
   *   The Event
   */
  protected function extract(\SimpleXMLElement $item) {
    $item_xcal = $item->children('urn:ietf:params:xml:ns:xcal');
    $item->registerXPathNamespace('x-trumba', 'http://schemas.trumba.com/rss/x-trumba');

    $event = new EventBase();
    $event->setId($item->guid->__toString());

    // Title.
    $event->setTitle($item->title->__toString());

    // Date/Time.
    $start = new \DateTime($item_xcal->dtstart->__toString());
    $event->setStartDateTime(new DrupalDateTime($start->format('Y-m-d\TH:i:s'), $start->format('e')));
    $end = new \DateTime($item_xcal->dtend->__toString());
    $event->setEndDateTime(new DrupalDateTime($end->format('Y-m-d\TH:i:s'), $end->format('e')));

    // Body.
    $body = $item_xcal->description->__toString();
    // Ensure that the body contains paragraphs.
    $body = '<p>' . implode('</p><p>', array_filter(explode("\n", $body))) . '</p>';
    $event->setBody($body);
    // No separate blurb is available.
    // Open-to-the-Public.
    $elements = $item->xpath("x-trumba:customfield[@id='623']");
    foreach ($elements as $element) {
      if (preg_match('/Open to the public/i', $element->__toString())) {
        $event->setOpenToThePublic(TRUE);
      }
      elseif (preg_match('/Closed to the public/i', $element->__toString())) {
        $event->setOpenToThePublic(FALSE);
      }
    }

    // Location.
    $location_string = $item_xcal->location->__toString();
    if (!empty($location_string)) {
      $query = \Drupal::entityQuery('taxonomy_term')
        ->condition('vid', 'locations')
        ->condition('name', $location_string);
      $tids = $query->execute();
      if (!empty($tids)) {
        $event->setLocationId(current($tids));
      }
      // Create a new Location term.
      else {
        $term = Term::create([
          'name' => $location_string,
          'vid' => 'locations',
        ]);
        $term->save();
        $event->setLocationId($term->id());
        \Drupal::logger('middlebury_event_sync')->notice('Created new location, %name.', ['%name' => $location_string]);
      }
    }

    // Admission Price.
    $elements = $item->xpath("x-trumba:customfield[@id='625']");
    foreach ($elements as $element) {
      $event->setOpenToThePublic($element->__toString());
    }

    // Contact -- used by MIIS.
    $elements = $item->xpath("x-trumba:customfield[@id='4407']");
    foreach ($elements as $element) {
      $event->setOrganizerName($element->__toString());
    }

    // Submitter Name -- used by Midd.
    $elements = $item->xpath("x-trumba:customfield[@id='884']");
    foreach ($elements as $element) {
      $event->setOrganizerName($element->__toString());
    }

    // Submitter Phone.
    $elements = $item->xpath("x-trumba:customfield[@id='885']");
    foreach ($elements as $element) {
      $event->setOrganizerTelephone($element->__toString());
    }

    // Submitter Email.
    $elements = $item->xpath("x-trumba:customfield[@id='886']");
    foreach ($elements as $element) {
      $event->setOrganizerEmail($element->__toString());
    }

    // Event types.
    $elements = $item->xpath("x-trumba:customfield[@id='12']");
    $event_types = [];
    foreach ($elements as $element) {
      $type = $element->__toString();
      // Strip off a '*' prefix.
      $type = preg_replace('/^\*/', '', $type);
      // Strip off a 'MIIS--' prefix.
      $type = preg_replace('/^MIIS\s*-+\s*/', '', $type);
      // Decode any HTML entities.
      $type = html_entity_decode($type);
      $event_types[] = $type;
    }
    $event->setEventTypes($event_types);

    return $event;
  }

}
