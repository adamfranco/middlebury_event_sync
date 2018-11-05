<?php

namespace Drupal\middlebury_event_sync\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\middlebury_event_sync\EventSourcePluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the EventSource add and edit forms.
 */
class EventSourceForm extends EntityForm {

  protected $entityQuery;
  protected $eventSourcePluginManager;

  /**
   * Constructs an EventSourceForm object.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The entity query.
   * @param \Drupal\middlebury_event_sync\EventSourcePluginManager $plugin_manager
   *   Our event source plugin manager.
   */
  public function __construct(QueryFactory $entity_query, EventSourcePluginManager $plugin_manager) {
    $this->entityQuery = $entity_query;
    $this->eventSourcePluginManager = $plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query'),
      $container->get('plugin.manager.event_source')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $event_source = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $event_source->label(),
      '#description' => $this->t("Label for the EventSource."),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $event_source->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$event_source->isNew(),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => [],
      '#default_value' => $event_source->getType(),
      '#description' => $this->t("The type of the data."),
      '#required' => TRUE,
    ];
    foreach ($this->eventSourcePluginManager->getDefinitions() as $pluginId => $pluginDefinition) {
      $form['type']['#options'][$pluginId] = $pluginDefinition['label'];
    }
    $form['uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URI'),
      '#maxlength' => 255,
      '#default_value' => $event_source->getUri(),
      '#description' => $this->t("The URI to source the data from."),
      '#required' => TRUE,
    ];
    $form['ttl'] = [
      '#type' => 'number',
      '#title' => $this->t('TTL'),
      '#default_value' => $event_source->getTtl(),
      '#description' => $this->t("The minimum number of seconds between fetches."),
      '#required' => TRUE,
    ];
    $form['time_shift'] = [
      '#type' => 'number',
      '#title' => $this->t('Time-shift'),
      '#default_value' => $event_source->getTimeShift(),
      '#description' => $this->t("The number of hours to shift the event time in the feed due to incorrect timezones in the source."),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
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
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $event_source = $this->entity;
    $status = $event_source->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label Event Source.', [
        '%label' => $event_source->label(),
      ]));
    }
    else {
      drupal_set_message($this->t('The %label Event Source was not saved.', [
        '%label' => $event_source->label(),
      ]));
    }

    $form_state->setRedirect('entity.event_source.collection');
  }

  /**
   * Helper function, checks whether an EventSource configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityQuery->get('event_source')
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
