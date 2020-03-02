<?php
namespace Drupal\search_facet;

use \Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\search_api\ParseMode\ParseModePluginManager;

use Drupal\search_facet\ExternalSearchApiPage;

class SearchFacets {
  private $entityTypeManager;

  private $parseModeManager;

  private $requestStack;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, RequestStack $request_stack, ParseModePluginManager $parse_mode_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->requestStack      = $request_stack;
    $this->parseModeManager  = $parse_mode_manager;
  }

  /**
   *
   */
  public function getSearchFacets($search_api_page, $after_format_callbacks = []) {
    $result = $this->getSearchQueryResult($search_api_page, $this->requestStack->getCurrentRequest());

    $facets = $result->getAllExtraData();

    return $this->formatFacetData($facets['search_api_facets'], $after_format_callbacks);
  }

  /**
   *
   */
  public function getSearchQueryResult($search_api_page, $request, $offset = 0, $length = 10) {
    $external_search_page = new ExternalSearchApiPage($this->parseModeManager);

    $search_api_page = $this->getSearchApiPage($search_api_page);
    $request         = $this->requestStack->getCurrentRequest();

    $query  = $external_search_page->prepareQueryForExternal($request, $search_api_page);
    $query->range($offset, $length);

    $result = $query->execute();

    return $result;
  }

  /**
   *
   */
  private function getSearchApiPage($search_api_page_name) {
    return $this->entityTypeManager
                ->getStorage('search_api_page')
                ->load($search_api_page_name);
  }

  /**
   *
   */
  private function formatFacetData($facet_data, $after_format_callbacks = []) {
    $formatted_facet_data = [];

    foreach ($facet_data as $facet_key => $facet_value) {
      $formatted_facet_data[$facet_key] = [];

      foreach($facet_value as $facet_filter) {
        $value = trim($facet_filter['filter'], '"');

        if (is_numeric($value)) {
          $value = intval($value);
        }

        $formatted_facet_data[$facet_key][$value] = $value;
      }

      $formatted_facet_data[$facet_key] = $this->executeFormatCallback(
                                                  $after_format_callbacks,
                                                  $facet_key,
                                                  $formatted_facet_data[$facet_key]
                                                );
    }

    return $formatted_facet_data;
  }

  /**
   *
   */
  private function executeFormatCallback($after_format_callbacks, $key, $data_set) {
    if (!empty($after_format_callbacks[$key]) && is_callable($after_format_callbacks[$key])) {
      return $after_format_callbacks[$key]($data_set);
    }

    return $data_set;
  }
}
