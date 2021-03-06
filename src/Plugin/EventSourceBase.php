<?php

namespace Drupal\middlebury_event_sync\Plugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\middlebury_event_sync\EventSourceInterface;
use Drupal\middlebury_event_sync\EventSourcePluginInterface;

/**
 * Base class for Event source plugins to handle persisting their configuration.
 */
abstract class EventSourceBase extends PluginBase implements EventSourcePluginInterface {

  /**
   * The name of the provider that owns this event source.
   *
   * @var string
   */
  public $provider;

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The Event Source config instance.
   *
   * @var \Drupal\middlebury_event_sync\EventSourceInterface
   */
  protected $configInstance;

  /**
   * The Event Source TTL.
   *
   * @var int
   */
  protected $ttl = 3600;

  /**
   * The Event Source time-shift in hours.
   *
   * @var int
   */
  protected $timeShift = 0;

  /**
   * Is this EventSource enabled for sync?
   *
   * It may be desired to keep an EventSource as a reference for old events,
   * or to prepare a new one. In neither case would we want to currently import
   * Events.
   *
   * @var boolean
   */
  protected $enabled = true;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->provider = $this->pluginDefinition['provider'];

    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    if (isset($configuration['ttl'])) {
      $this->setTtl((int) $configuration['ttl']);
    }
    if (isset($configuration['time_shift'])) {
      $this->setTimeShift((int) $configuration['time_shift']);
    }
    if (isset($configuration['enabled'])) {
      $this->setEnabled((boolean) $configuration['enabled']);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return [
      'id' => $this->getPluginId(),
      'provider' => $this->pluginDefinition['provider'],
      'ttl' => $this->getTtl(),
      'time_shift' => $this->getTimeShift(),
      'enabled' => $this->getEnabled(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'provider' => $this->pluginDefinition['provider'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->pluginDefinition['type'];
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function setConfigInstance(EventSourceInterface $config_instance) {
    $this->configInstance = $config_instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigInstance() {
    return $this->configInstance;
  }

  /**
   * {@inheritdoc}
   */
  public function setTtl($ttl) {
    $this->ttl = intval($ttl);
  }

  /**
   * {@inheritdoc}
   */
  public function getTtl() {
    return $this->ttl;
  }

  /**
   * {@inheritdoc}
   */
  public function setTimeShift($time_shift) {
    $this->timeShift = intval($time_shift);
  }

  /**
   * {@inheritdoc}
   */
  public function getTimeShift() {
    return $this->timeShift;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnabled($enabled) {
    $this->enabled = boolval($enabled);
  }

  /**
   * {@inheritdoc}
   */
  public function getEnabled() {
    return $this->enabled;
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
    $form['ttl'] = [
      '#type' => 'number',
      '#title' => $this->t('TTL'),
      '#default_value' => $this->getTtl(),
      '#description' => $this->t("The minimum number of seconds between fetches."),
      '#required' => TRUE,
    ];
    $form['time_shift'] = [
      '#type' => 'number',
      '#title' => $this->t('Time-shift'),
      '#default_value' => $this->getTimeShift(),
      '#description' => $this->t("The number of hours to shift the event time in the feed due to incorrect timezones in the source."),
      '#required' => TRUE,
    ];
    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $this->getEnabled(),
      '#description' => $this->t("Should this event source import events when cron runs?."),
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
    if (!is_numeric($form_state->getValue('ttl'))) {
      $form_state->setErrorByName('ttl', $this->t('The TTL must be an integer >= 0.'));
    }
    if ($form_state->getValue('ttl') < 0) {
      $form_state->setErrorByName('ttl', $this->t('The TTL must be an integer >= 0.'));
    }
    if (!is_numeric($form_state->getValue('time_shift'))) {
      $form_state->setErrorByName('time_shift', $this->t('The Time Shift must be an integer.'));
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
    $this->setTtl($form_state->getValue('ttl'));
    $this->setTimeShift($form_state->getValue('time_shift'));
    $this->setEnabled($form_state->getValue('enabled'));
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    // If plugins have additional dependencies they should declare them.
    return [];
  }

}
