<?php
// $Id$

/**
 * Views handler configuration form.
 */
function data_search_admin_form(&$form_state, $table) {
  $schema = $table->get('table_schema');
  $meta = $table->get('meta');

  $form = array();

  // Keep table.
  $form['#table'] = $table;

  // Existing fields.
  $form['fields'] = array('#tree' => TRUE);
  if (isset($schema['fields'])) {
    $enabled_fields = data_search_get_fields($table);
    foreach ($schema['fields'] as $field_name => $field) {
      $form['fields'][$field_name] = array();
      $form['fields'][$field_name]['name'] = array('#value' => $field_name);
      $form['fields'][$field_name]['search'] = array(
        '#type' => 'checkbox',
        '#default_value' => in_array($field_name, $enabled_fields),
      );
    }
  }
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );
  return $form;
}

/**
 * Submit handler for search form.
 */
function data_search_admin_form_submit(&$form, &$form_state) {
  $table = $form['#table'];
  $meta = $table->get('meta');
  if (isset($form_state['values']['fields'])) {
    foreach ($form_state['values']['fields'] as $field_name => $settings) {
      foreach ($settings as $setting => $value) {
        $meta['fields'][$field_name][$setting] = $value;
      }
    }
  }
  $table->update(array('meta' => $meta));
  views_invalidate_cache();  
}

/**
 * Theme data_search_admin_form.
 */
function theme_data_search_admin_form($form) {

  // Format existing fields.
  $rows = array();
  foreach (element_children($form['fields']) as $e) {
    $row = array();
    foreach (element_children($form['fields'][$e]) as $f) {
      $row[] = drupal_render($form['fields'][$e][$f]);
    }
    $rows[] = $row;
  }

  $header = array(t('Name'), t('Search index'));
  $output .= theme('table', $header, $rows);
  $output .= drupal_render($form);
  return $output;
}