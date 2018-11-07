<?php

namespace Drupal\middlebury_event_sync\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for building the event-source instance add form.
 */
class EventSourceAddController extends ControllerBase {

  /**
   * Build the event-source instance add form.
   *
   * @param string $plugin_id
   *   The plugin ID for the event-source instance.
   *
   * @return array
   *   The event-source instance edit form.
   */
  public function eventSourceAddConfigureForm($plugin_id) {
    // Create a event-source entity.
    $entity = $this->entityManager()->getStorage('event_source')->create(['plugin' => $plugin_id]);

    return $this->entityFormBuilder()->getForm($entity);
  }

}
