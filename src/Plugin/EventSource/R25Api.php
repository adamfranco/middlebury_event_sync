<?php

namespace Drupal\middlebury_event_sync\Plugin\EventSource;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\middlebury_event_sync\EventSourcePluginInterface;
use Drupal\middlebury_event_sync\Plugin\EventSourceBase;

/**
 * Plugin definition for the "R25Api" plugin.
 *
 * @EventSource(
 *   id = "r25api",
 *   label = @Translation("R25 API"),
 * )
 */
class R25Api extends EventSourceBase implements EventSourcePluginInterface {

  /**
   * The URI to fetch events from.
   *
   * @var string
   */
  protected $uri;

  /**
   * The username to use when fetching events.
   *
   * @var string
   */
  protected $username;

  /**
   * The password to use when fetching events.
   *
   * @var string
   */
  protected $password;

  /**
   * The URI to use when fetching events.
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
   * The username to use when fetching events.
   *
   * @param string $username
   *   The URI to fetch from.
   */
  public function setUsername($username) {
    $this->username = $username;
  }

  /**
   * Answer the username to use when fetching events.
   *
   * @return string
   *   The username.
   */
  public function getUsername() {
    if (empty($this->username)) {
      return '';
    }
    else {
      return $this->username;
    }
  }

  /**
   * The password to use when fetching events.
   *
   * @param string $password
   *   The URI to fetch from.
   */
  public function setPassword($password) {
    $this->password = $password;
  }

  /**
   * Answer the password to use when fetching events.
   *
   * @return string
   *   The password.
   */
  public function getPassword() {
    if (empty($this->password)) {
      return '';
    }
    else {
      return $this->password;
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
    if (isset($configuration['username'])) {
      $this->setUsername($configuration['username']);
    }
    if (isset($configuration['password'])) {
      $this->setPassword($configuration['password']);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    $configuration = parent::getConfiguration();
    $configuration['uri'] = $this->getUri();
    $configuration['username'] = $this->getUsername();
    $configuration['password'] = $this->getPassword();
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
      '#description' => $this->t("The base-URI of the 25Live web-service. Example: https://webservices.collegenet.com/r25ws/wrd/middlebury/run"),
      '#required' => TRUE,
    ];
    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#maxlength' => 80,
      '#default_value' => $this->getUsername(),
      '#description' => $this->t("The username to use when fetching data."),
      '#required' => FALSE,
    ];
    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#maxlength' => 80,
      '#default_value' => $this->getPassword(),
      '#description' => $this->t("The password to use when fetching data."),
      '#required' => FALSE,
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
    parent::validateConfigurationForm($form, $form_state);
    $this->setUri($form_state->getValue('uri'));
    $this->setUsername($form_state->getValue('username'));
    $this->setPassword($form_state->getValue('password'));
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
    throw new \Exception('Not yet implemented');
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
    throw new \Exception('Not yet implemented');
  }

}
