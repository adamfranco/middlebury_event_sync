<?php

namespace Drupal\middlebury_event_sync;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining an Example entity.
 */
interface EventSourceInterface extends ConfigEntityInterface {

  /**
   * Returns the plugin instance.
   *
   * @return \Drupal\Core\Block\BlockPluginInterface
   *   The plugin instance for this block.
   */
  public function getPlugin();

  /**
   * Returns the plugin ID.
   *
   * @return string
   *   The plugin ID for this block.
   */
  public function getPluginId();

}
