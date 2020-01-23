<?php
namespace Drupal\drupal_realestate_core\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;

use Drupal\drupal_realestate_core\Constants\Location;

class PropertySearchForm extends FormBase {
  public function getFormId() {
    return 'property_search_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $after_foramt_callbacks = [
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
                            ->getSearchFacets('property_search', $after_foramt_callbacks);

    $form['location'] = [
      '#type' => 'select',
      '#options' => $facet_filters['field_property_address_state'],
      '#title' => 'Location',
      '#attributes' => [
        'class' => [ 'wide' ]
      ]
    ];

    $form['property_type'] = [
      '#type' => 'select',
      '#options' => $facet_filters['field_property_type'],
      '#title' => 'Property Type',
      '#attributes' => [
        'class' => [ 'wide' ]
      ]
    ];

    $form['min_price'] = [
      '#type'  => 'hidden',
      '#attributes' => [
        'class' => [ 'property-search-price-min' ]
      ],
      '#value' => end($facet_filters['field_listing_price'])
    ];

    $form['max_price'] = [
      '#type' => 'hidden',
      '#attributes' => [
        'class' => [ 'property-search-price-max' ]
      ],
      '#value' => reset($facet_filters['field_listing_price'])
    ];

    $form['bed_room'] = [
      '#type' => 'select',
      '#options' => $facet_filters['field_bed_quantity'],
      '#title' => 'Bed Room',
      '#attributes' => [
        'class' => [ 'wide' ]
      ]
    ];

    $form['bath_room'] = [
      '#type' => 'select',
      '#options' => $facet_filters['field_bathroom_quantity'],
      '#title' => 'Bath Room',
      '#attributes' => [
        'class' => [ 'wide' ]
      ]
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'submit',
      '#attributes' => [
        'class' => [ 'search-form-btn' ]
      ],
      '#ajax' => [
        'callback' => '::sampleAjaxResponse'
      ]
    ];

    $form['#attached']['library'][] = 'drupal_realestate_core/field_components';

    return $form;
  }

  public function sampleAjaxResponse() {
    $response = new AjaxResponse();

    return $response;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
