<?php
namespace Drupal\drupal_realestate_core\Helpers;

class Config {
  public static function setHomePage($url) {
    $config = self::getCurrentConfig();

    $config->set('page.front', $url)->save();
  }

  private static function getCurrentConfig() {
    return \Drupal::configFactory()->getEditable('system.site');
  }
}
