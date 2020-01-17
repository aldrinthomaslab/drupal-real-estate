<?php

namespace Drupal\drupal_realestate_core\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;

class PropertySearchForm extends FormBase {
  public function getFormId() {
    return 'property_search_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['location'] = [
      '#type' => 'select',
      '#options' => [],
      '#title' => 'Location',
      '#attributes' => [
        'class' => [ 'wide' ]
      ]
    ];

    $form['property_type'] = [
      '#type' => 'select',
      '#options' => [],
      '#title' => 'Property Type',
      '#attributes' => [
        'class' => [ 'wide' ]
      ]
    ];

    $form['min_price'] = [
      '#type' => 'textfield'
    ];

    $form['max_price'] = [
      '#type' => 'textfield'
    ];

    $form['bed_room'] = [
      '#type' => 'select',
      '#options' => [],
      '#title' => 'Bed Room',
      '#attributes' => [
        'class' => [ 'wide' ]
      ]
    ];

    $form['bath_room'] = [
      '#type' => 'select',
      '#options' => [],
      '#title' => 'Bath Room',
      '#attributes' => [
        'class' => [ 'wide' ]
      ]
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'submit'
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
