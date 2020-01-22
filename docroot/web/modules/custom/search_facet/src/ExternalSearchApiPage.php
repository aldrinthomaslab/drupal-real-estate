<?php
namespace Drupal\search_facet;

use Symfony\Component\HttpFoundation\Request;
use Drupal\search_api_page\SearchApiPageInterface;

use Drupal\search_api_page\Controller\SearchApiPageController;

class ExternalSearchApiPage extends SearchApiPageController {
  public function prepareQueryForExternal(Request $request, SearchApiPageInterface $search_api_page) {
    return $this->prepareQuery($request, $search_api_page);
  }
}
