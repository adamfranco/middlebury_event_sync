<?php

namespace Drupal\middlebury_event_sync\Form;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Plugin\PluginFormFactoryInterface;
use Drupal\Core\Plugin\PluginWithFormsInterface;
use Drupal\middlebury_event_sync\EventSourcePluginInterface;
use Drupal\middlebury_event_sync\EventSourcePluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the EventSource add and edit forms.
 */
class EventSourceForm extends EntityForm {

  protected $entityQuery;
  protected $eventSourcePluginManager;

  /**
   * The plugin form manager.
   *
   * @var \Drupal\Core\Plugin\PluginFormFactoryInterface
   */
  protected $pluginFormFactory;

  /**
   * Constructs an EventSourceForm object.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The entity query.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager
   *   Our event source plugin manager.
   * @param \Drupal\Core\Plugin\PluginFormFactoryInterface $plugin_form_manager
   *   The plugin form manager.
   */
  public function __construct(QueryFactory $entity_query, PluginManagerInterface $plugin_manager, PluginFormFactoryInterface $plugin_form_manager) {
    $this->entityQuery = $entity_query;
    $this->eventSourcePluginManager = $plugin_manager;
    $this->pluginFormFactory = $plugin_form_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query'),
      $container->get('plugin.manager.event_source'),
      $container->get('plugin_form.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#description' => $this->t("Label for the EventSource."),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$entity->isNew(),
    ];

    $form['#tree'] = TRUE;
    $form['settings'] = [];
    $subform_state = SubformState::createForSubform($form['settings'], $form, $form_state);
    $form['settings'] = $this->getPluginForm($entity->getPlugin())->buildConfigurationForm($form['settings'], $subform_state);

    return $form;
  }

  /**
   * Answer the default plugin to use.
   */
  protected function getDefaultPluginId(array $pluginDefinitions) {
    foreach ($this->eventSourcePluginManager->getDefinitions() as $pluginId => $pluginDefinition) {
      // Return the first one.
      return $pluginId;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // The EventSource Entity form puts all event-source plugin form elements in the
    // settings form element, so just pass that to the event-source plugin for validation.
    $this->getPluginForm($this->entity->getPlugin())->validateConfigurationForm($form['settings'], SubformState::createForSubform($form['settings'], $form, $form_state));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $entity = $this->entity;
    // The EventSource Entity form puts event-source block plugin form elements in the
    // settings form element, so just pass that to the event-source for submission.
    $sub_form_state = SubformState::createForSubform($form['settings'], $form, $form_state);
    // Call the plugin submit handler.
    $block = $entity->getPlugin();
    $this->getPluginForm($block)->submitConfigurationForm($form, $sub_form_state);

    // Save the settings of the plugin.
    $this->save($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label Event Source.', [
        '%label' => $entity->label(),
      ]));
    }
    else {
      drupal_set_message($this->t('The %label Event Source was not saved.', [
        '%label' => $entity->label(),
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

  /**
   * Retrieves the plugin form for a given event-source and operation.
   *
   * @param \Drupal\middlebury_event_sync\EventSourcePluginInterface $event_source
   *   The event-source plugin.
   *
   * @return \Drupal\Core\Plugin\PluginFormInterface
   *   The plugin form for the event-source.
   */
  protected function getPluginForm(EventSourcePluginInterface $event_source) {
    if ($event_source instanceof PluginWithFormsInterface) {
      return $this->pluginFormFactory->createInstance($event_source, 'configure');
    }
    return $event_source;
  }

}
