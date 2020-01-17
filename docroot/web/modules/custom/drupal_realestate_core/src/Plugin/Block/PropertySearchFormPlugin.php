<?php

namespace Drupal\drupal_realestate_core\Plugin\Block;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a custom footer block.
 *
 * @Block(
 *   id = "drupal_realestate_searchform_block",
 *   admin_label = @Translation("Property Search Form Block"),
 *   category = @Translation("Drupal Realestate Core"),
 * )
 */

class PropertySearchFormPlugin extends BlockBase {
  public function build() {
    $update_form = \Drupal::formBuilder()
                          ->getForm('Drupal\drupal_realestate_core\Form\PropertySearchForm');

    return $update_form;
  }
}
