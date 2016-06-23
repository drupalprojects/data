<?php

namespace Drupal\data\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class TableConfigForm.
 *
 * @package Drupal\data\Form
 */
class TableConfigForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $data_table_config = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $data_table_config->label(),
      '#description' => $this->t("Label for the Data Table."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $data_table_config->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\data\Entity\TableConfig::load',
      ),
      '#disabled' => !$data_table_config->isNew(),
    );

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
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

}
