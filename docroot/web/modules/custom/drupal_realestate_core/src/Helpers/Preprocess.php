<?php
namespace Drupal\drupal_realestate_core\Helpers;

class Preprocess {
  /**
   *
   */
  public static function addBlockSuggestion($suggestions, $variables, $block_name, $suggestion) {
    $current_suggestions = $suggestions;

    $blockId = self::getBlockIdentifier($variables);

    if (empty($blockId)) {
      return $suggestions;
    }

    if (!self::isNameInMatch($block_name, $blockId)) {
      return $suggestions;
    }

    $current_suggestions[] = $suggestion;

    return $current_suggestions;
  }

  /**
   *
   */
  private static function isNameInMatch($reference, $entity_name) {
    if (is_array($reference)) {
      return in_array($entity_name, $reference);
    }

    return $entity_name === $reference;
  }

  /**
   *
   */
  private static function getBlockIdentifier($variables) {
    if (!empty($variables['elements']['content']['#name'])) {
      return $variables['elements']['content']['#name'];
    }

    if (!empty($variables['elements']['#id'])) {
      return $variables['elements']['#id'];
    }

    if (!empty($variables['elements']['content']['#id'])) {
      return $variables['elements']['content']['#id'];
    }

    return;
  }
}
