<?php

namespace Drupal\data\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Data Table entity.
 *
 * @ConfigEntityType(
 *   id = "data_table_config",
 *   label = @Translation("Data Table"),
 *   handlers = {
 *     "list_builder" = "Drupal\data\TableConfigListBuilder",
 *     "form" = {
 *       "add" = "Drupal\data\Form\TableConfigForm",
 *       "edit" = "Drupal\data\Form\TableConfigForm",
 *       "delete" = "Drupal\data\Form\TableConfigDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\data\TableConfigHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "data_table_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/data/{data_table_config}",
 *     "add-form" = "/admin/structure/data/add",
 *     "edit-form" = "/admin/structure/data/{data_table_config}/edit",
 *     "delete-form" = "/admin/structure/data/{data_table_config}/delete",
 *     "collection" = "/admin/structure/data"
 *   }
 * )
 */
class TableConfig extends ConfigEntityBase implements TableConfigInterface {

  /**
   * The Data Table ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Data Table label.
   *
   * @var string
   */
  protected $label;

}
