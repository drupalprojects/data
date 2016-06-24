<?php

namespace Drupal\data\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class TableConfigForm.
 *
 * @package Drupal\data\Form
 */
class TableConfigForm extends EntityForm {

  /** @var int $step 0 - name and number of fields 1 - fields edit. */
  protected $step;

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $data_table_config = $this->entity;

    $number_of_fields = $form_state->getValue('field_num');
    $this->step = $number_of_fields ? 1 : 0;

    // Multistep form.
    if (!$this->step) {
      // First form, ask for the database table name.
      $form['title'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Table title'),
        '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
        '#default_value' => $data_table_config->label(),
        '#description' => $this->t('Table title.'),
        '#required' => TRUE,
      );

      $form['id'] = array(
        '#type' => 'machine_name',
        '#default_value' => $data_table_config->id(),
        '#machine_name' => array(
          'exists' => '\Drupal\data\Entity\TableConfig::load',
          'source' => array('title'),
        ),
        '#description' => $this->t('Machine readable name of the table - e. g. "my_table". Must only contain lower case letters and _.'),
        '#disabled' => !$data_table_config->isNew(),
      );

      $form['field_num'] = array(
        '#type' => 'textfield',
        '#title' => t('Number of fields'),
        '#description' => t('The number of fields this table should contain.'),
        '#default_value' => 1,
        '#required' => TRUE,
      );
      $form['actions']['submit']['#value'] = t('Next');
    }
    else {
      // Second form, ask for the database field names.
      $form['help']['#markup'] = t('Define the fields of the new table.');
      $form['fields'] = array(
        '#tree' => TRUE,
      );
      for ($i = 0; $i < $number_of_fields; $i++) {
        $form['fields']['field_' . $i] = $this->fieldForm(TRUE);
      }
      $form['actions']['submit']['#value'] = t('Create');
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('field_num')) {
      $form_state->setRebuild();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    // No need to save entity at the first step.
    if (!$this->step) {
      return;
    }
    $data_table_config = $this->entity;
    $status = $data_table_config->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Data Table.', [
          '%label' => $data_table_config->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Data Table.', [
          '%label' => $data_table_config->label(),
        ]));
    }
    $form_state->setRedirectUrl($data_table_config->urlInfo('collection'));
  }

  /**
   * Helper function that generates a form snippet for defining a field.
   *
   * formerly known as _data_ui_field_form().
   */
  protected function fieldForm($required = FALSE) {
    $form = array();
    $form['#tree'] = TRUE;
    $form['name'] = array(
      '#type' => 'textfield',
      '#size' => 20,
      '#required' => $required,
    );
    $form['label'] = array(
      '#type' => 'textfield',
      '#size' => 20,
    );
    $form['type'] = array(
      '#type' => 'select',
      '#options' => data_get_field_types(),
    );
    $form['size'] = array(
      '#type' => 'select',
      '#options' => data_get_field_sizes(),
    );
    $form['unsigned'] = array(
      '#type' => 'checkbox',
    );
    $form['index'] = array(
      '#type' => 'checkbox',
    );
    $form['primary'] = array(
      '#type' => 'checkbox',
    );
    return $form;
  }

}
