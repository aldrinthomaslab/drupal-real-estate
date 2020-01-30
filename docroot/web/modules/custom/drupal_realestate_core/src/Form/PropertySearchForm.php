<?php
namespace Drupal\drupal_realestate_core\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\drupal_realestate_core\Constants\Location;

class PropertySearchForm extends FormBase {
  private $parameters = [
    [
      'facet_key' => 'listing_price',
      'field_key' => 'field_listing_price'
    ],
    [
      'facet_key'      => 'number_of_bathrooms',
      'field_key'      => 'field_bathroom_quantity',
      'form_field_key' => 'bath_room'
    ],
    [
      'facet_key'      => 'number_of_beds',
      'field_key'      => 'field_bed_quantity',
      'form_field_key' => 'bed_room'
    ],
    [
      'facet_key'      => 'field_property_address_state',
      'field_key'      => 'field_property_address_state',
      'form_field_key' => 'location'
    ],
    [
      'facet_key'      => 'property_type',
      'field_key'      => 'field_property_type',
      'form_field_key' => 'property_type'
    ]
  ];

  private $request_stack;

  public function __construct(RequestStack $request_stack) {
    $this->request_stack = $request_stack;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')
    );
  }

  public function getFormId() {
    return 'property_search_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $after_format_callbacks = [
      'field_property_type' => function ($data_set) {
        $formatted_terms = [];

        $result = \Drupal::entityTypeManager()
                         ->getStorage('taxonomy_term')
                         ->loadMultiple($data_set);

        foreach ($result as $key => $term) {
          $formatted_terms[$key] = $term->getName();
        }

        return $formatted_terms;
      },
      'field_property_address_state' => function ($data_set) {
        $formatted_terms = [];

        foreach ($data_set as $state_key => $state_text) {
          $formatted_terms[$state_key] = Location::US_STATES[$state_key];
        }

        return $formatted_terms;
      }
    ];

    $facet_filters = \Drupal::service('search_facet.facet_field_helper')
                            ->getSearchFacets('property_search', $after_format_callbacks);

    $query_values = $this->getSearchQueryValues($this->request_stack->getCurrentRequest()->get('property_filter'));

    $min_price = min($facet_filters['field_listing_price']);
    $max_price = max($facet_filters['field_listing_price']);

    $form['location'] = [
      '#type' => 'select',
      '#options' => $this->addDropdownAllOption($facet_filters['field_property_address_state']),
      '#title' => 'Location',
      '#attributes' => [
        'class' => [ 'wide' ]
      ],
      '#default_value' => (!empty($query_values['field_property_address_state'])) ? $query_values['field_property_address_state'] : 'all'
    ];

    $form['property_type'] = [
      '#type' => 'select',
      '#options' => $this->addDropdownAllOption($facet_filters['field_property_type']),
      '#title' => 'Property Type',
      '#attributes' => [
        'class' => [ 'wide' ]
      ],
      '#default_value' => (!empty($query_values['property_type'])) ? intval($query_values['property_type']) : 'all'
    ];

    $form['min_price'] = [
      '#type'  => 'hidden',
      '#attributes' => [
        'class' => [ 'property-search-price-min-val' ]
      ],
      '#default_value' => (!empty($query_values['listing_price'])) ? floatval($query_values['listing_price'][0]['value']) + 1 : $min_price
    ];

    $form['ref_min_price'] = [
      '#type'  => 'hidden',
      '#attributes' => [
        'class' => [ 'property-search-price-min' ]
      ],
      '#default_value' => $min_price
    ];

    $form['max_price'] = [
      '#type' => 'hidden',
      '#attributes' => [
        'class' => [ 'property-search-price-max-val' ]
      ],
      '#default_value' => (!empty($query_values['listing_price'])) ? floatval($query_values['listing_price'][1]['value']) - 1 : $max_price
    ];

    $form['ref_max_price'] = [
      '#type'  => 'hidden',
      '#attributes' => [
        'class' => [ 'property-search-price-max' ]
      ],
      '#default_value' => $max_price
    ];

    $form['bed_room'] = [
      '#type' => 'select',
      '#options' => $this->addDropdownAllOption($facet_filters['field_bed_quantity']),
      '#title' => 'Bed Room',
      '#attributes' => [
        'class' => [ 'wide' ]
      ],
      '#default_value' => (!empty($query_values['number_of_beds'])) ? $query_values['number_of_beds'] : 'all'
    ];

    $form['bath_room'] = [
      '#type' => 'select',
      '#options' => $this->addDropdownAllOption($facet_filters['field_bathroom_quantity']),
      '#title' => 'Bath Room',
      '#attributes' => [
        'class' => [ 'wide' ]
      ],
      '#default_value' => (!empty($query_values['number_of_bathrooms'])) ? intval($query_values['number_of_bathrooms']) : 'all'
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'submit',
      '#attributes' => [
        'class' => [ 'search-form-btn' ]
      ]
    ];

    $form['#attached']['library'][] = 'drupal_realestate_core/field_components';

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $redirect_url = '/search';
    $redirect_url .= '?' . $this->buildSearchParameters($values);
    $redirect_response = new RedirectResponse($redirect_url);

    $form_state->setResponse($redirect_response);
  }

  private function getSearchQueryValues($search_query) {
    $query_array = [];

    if (!empty($search_query)) {
      foreach ($search_query as $query) {
        $raw_data      = $this->extractQueryData($query);
        $query_array[$raw_data['key']] = $raw_data['value'];
      }
    }

    return $query_array;
  }

  private function addDropdownAllOption($options) {
    $current_options = [ 'all' => 'All' ] + $options;

    return $current_options;
  }

  /**
   *
   */
  private function extractQueryData($query) {
    $matches = [];
    preg_match('/^(.+?)\:(.+)$/', $query, $matches);

    if (preg_match('/^\((.+\:.+\,?)+\)$/', $matches[2])) {
      $array_string = trim($matches[2], '()');
      $array_string = explode(',', $array_string);
      $multi_return = [];

      foreach ($array_string as $string) {
        $multi_return[] = $this->extractQueryData($string);
      }

      return [
        'key'   => $matches[1],
        'value' => $multi_return
      ];
    }

    return [
      'key'   => $matches[1],
      'value' => $matches[2]
    ];
  }

  /**
   *
   */
  private function buildSearchParameters($values) {
    $search_url_param = '';

    foreach ($this->parameters as $field_keys => $search_fields) {
      if ($search_fields['field_key'] !== 'field_listing_price' && $values[$search_fields['form_field_key']] === 'all') {
        continue;
      }

      $search_url_param .= 'property_filter' . '[' . $field_keys . ']=';

      if ($search_fields['field_key'] === 'field_listing_price') {
        $range_value = $search_fields['facet_key'] . ':(';

        $min_price = intval($values['min_price']) - 1;
        $max_price = intval($values['max_price']) + 1;

        $range_value .= 'min:' . $min_price . ',';
        $range_value .= 'max:' . $max_price . ')';

        $search_url_param .= urlencode($range_value);
      } else {
        $search_url_param .= urlencode($search_fields['facet_key'] . ':' . $values[$search_fields['form_field_key']]);
      }

      $search_url_param .= '&';
    }

    return trim($search_url_param, '\&');
  }
}
