<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$conn->multi_query(<<<EOT
DELETE FROM eav_attribute WHERE entity_type_id=9;
DELETE FROM eav_entity_attribute WHERE entity_type_id=9;

INSERT INTO `eav_attribute` (`attribute_id`, `entity_type_id`, `attribute_code`, `attribute_model`, `backend_model`, `backend_type`, `backend_table`, `frontend_model`, `frontend_input`, `frontend_label`, `frontend_class`, `source_model`, `is_global`, `is_visible`, `is_required`, `is_user_defined`, `default_value`, `is_searchable`, `is_filterable`, `is_comparable`) VALUES
(111,9,'name',NULL,NULL,'varchar',NULL,NULL,'text','Name',NULL,NULL,0,1,1,0,NULL,0,0,0),
(112,9,'description',NULL,NULL,'text',NULL,NULL,'textarea','Description',NULL,NULL,0,1,0,0,NULL,0,0,0),
(113,9,'image',NULL,'catalog_entity/category_attribute_backend_image','varchar',NULL,NULL,'image','Image',NULL,NULL,1,1,0,0,NULL,0,0,0),
(114,9,'meta_title',NULL,NULL,'varchar',NULL,NULL,'text','Meta Title',NULL,NULL,1,1,0,0,NULL,0,0,0),
(115,9,'meta_keywords',NULL,NULL,'text',NULL,NULL,'textarea','Meta Keywords',NULL,NULL,1,1,0,0,NULL,0,0,0),
(116,9,'meta_description',NULL,NULL,'text',NULL,NULL,'textarea','Meta Description',NULL,NULL,1,1,0,0,NULL,0,0,0),
(117,9,'landing_page',NULL,NULL,'int',NULL,NULL,'select','Landing Page',NULL,'catalog_entity/category_attribute_source_page',1,1,0,0,NULL,0,0,0),
(118,9,'display_mode',NULL,NULL,'varchar',NULL,NULL,'select','Display Mode',NULL,'catalog_entity/category_attribute_source_mode',1,1,0,0,NULL,0,0,0),
(119,9,'is_active',NULL,NULL,'static',NULL,NULL,'select','Is Active',NULL,'eav/entity_attribute_source_boolean',1,1,0,0,NULL,0,0,0),
(120,9,'is_anchor',NULL,NULL,'int',NULL,NULL,'select','Is Anchor',NULL,'eav/entity_attribute_source_boolean',1,1,0,0,NULL,0,0,0),
(121,9,'all_children',NULL,'catalog_entity/category_attribute_backend_tree_children','text',NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,NULL,0,0,0),
(122,9,'path_in_store',NULL,'catalog_entity/category_attribute_backend_tree_path','text',NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,NULL,0,0,0),
(123,9,'children',NULL,'catalog_entity/category_attribute_backend_tree_children','text',NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,NULL,0,0,0);

INSERT INTO `eav_entity_attribute` (`entity_type_id`, `attribute_set_id`, `attribute_group_id`, `attribute_id`, `sort_order`) VALUES 
(9,12,7,111,1),
(9,12,7,112,2),
(9,12,7,113,3),
(9,12,7,114,4),
(9,12,7,115,5),
(9,12,7,116,6),
(9,12,7,117,7),
(9,12,7,118,8),
(9,12,7,119,9),
(9,12,7,120,10),
(9,12,7,121,11),
(9,12,7,122,12),
(9,12,7,123,13);
 
EOT
);