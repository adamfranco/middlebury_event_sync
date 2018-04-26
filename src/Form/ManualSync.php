<?php

namespace Drupal\middlebury_event_sync\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a manual synchronization UI.
 */
class ManualSync extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'middlebury_event_sync_manual_sync';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = [];

    // Page title field.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sync Events'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $messages = middlebury_event_sync_sync_all();
    foreach ($messages as $message) {
      drupal_set_message($message);
    }
  }

}
