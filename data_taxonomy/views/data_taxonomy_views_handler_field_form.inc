<?php
// $Id$

/**
 * @todo
 */
class data_taxonomy_views_handler_field_form extends views_handler_field {
  protected $id;

  /**
   * Describe provided options and default values.
   */
  function option_definition() {
    $options = parent::option_definition();
    $options['vocabularies'] = array('default' => array());
    return $options;
  }

  /**
   * Suppress other options and provide a selection of vocabularies for which
   * tagging forms will be displayed.
   */
  function options_form(&$form, &$form_state) {
    $options = array();
    $table = data_get_table($this->table);
    $meta = $table->get('meta');
    if (!empty($meta['data_taxonomy']['vocabularies'])) {
      foreach ($meta['data_taxonomy']['vocabularies'] as $vid) {
        $options[$vid] = taxonomy_vocabulary_load($vid)->name;
      }
    }
    if (!empty($options)) {
      $form['vocabularies'] = array(
        '#title' => t('Enabled vocabularies'),
        '#description' => t('Display a tagging form for each of the selected vocabularies.'),
        '#type' => 'checkboxes',
        '#options' => $options,
        '#default_value' => $this->options['vocabularies'],
      );
    }
  }

  /**
   * Add field on which we join.
   */
  function query() {
    if (user_access('edit data taxonomy relations')) {
      $this->ensure_my_table();

      // Add the id field for this table to the query.
      $table = data_get_table($this->table);
      $meta = $table->get('meta');
      if (!empty($meta['data_taxonomy']['vocabularies']) && isset($meta['data_taxonomy']['id'])) {
        $this->id = $this->query->add_field($this->table_alias, $meta['data_taxonomy']['id']);
      }
      $this->add_additional_fields();
    }
  }

  /**
   * Render form.
   */
  function render($values) {
    $output = '';
    if (user_access('edit data taxonomy relations')) {
      $table = data_get_table($this->table);
      $meta = $table->get('meta');
      if (!empty($meta['data_taxonomy']['vocabularies'])) {
        foreach ($meta['data_taxonomy']['vocabularies'] as $vid) {
          if (!empty($this->options['vocabularies'][$vid])) {
            $output .= drupal_get_form('data_taxonomy_tagging_form', $vid, $values->{$this->id}, $this->table);
          }
        }
      }
    }
    return $output;
  }

  /**
   * Omit label output.
   */
  function label() {
    return '';
  }
}