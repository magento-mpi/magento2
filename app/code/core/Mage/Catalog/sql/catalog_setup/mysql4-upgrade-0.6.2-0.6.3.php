<?php

$conn->query("DROP TABLE if exists `catalog_category_entity_gallery`");

$conn->query("

    CREATE TABLE `catalog_product_entity_gallery` (
      `value_id` int(11) NOT NULL auto_increment,
      `entity_type_id` smallint(5) unsigned NOT NULL default '0',
      `attribute_id` smallint(5) unsigned NOT NULL default '0',
      `store_id` smallint(5) unsigned NOT NULL default '0',
      `entity_id` int(10) unsigned NOT NULL default '0',
      `position` int(11) NOT NULL default '0',
      `value` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`value_id`),
      KEY `IDX_BASE` USING BTREE (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
      KEY `FK_ATTRIBUTE_GALLERY_ENTITY` (`entity_id`),
      KEY `FK_CATALOG_CATEGORY_ENTITY_GALLERY_ATTRIBUTE` (`attribute_id`),
      KEY `FK_CATALOG_CATEGORY_ENTITY_GALLERY_STORE` (`store_id`),
      CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_GALLERY_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_GALLERY_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_GALLERY_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8

");

$sql = $conn->select()
    ->from('eav_entity_type', array('entity_type_id'))
    ->where('entity_type_code = ?', 'catalog_product');
    
$res = $conn->fetchRow($sql);

$conn->query("INSERT INTO `eav_attribute` SET
    `entity_type_id` = '".$res['entity_type_id']."',
    `attribute_code` = 'gallery',
    `backend_model` = 'catalog_entity/product_attribute_backend_gallery',
    `backend_type` = 'varchar',
    `backend_table` = 'catalog_product_entity_gallery',
    `frontend_input` = 'gallery',
    `frontend_label` = 'Images',
    `is_global` = 1,
    `is_visible` = 1,
    `is_required` = 0,
    `is_user_defined` = 0,
    `is_searchable` = 0,
    `is_filterable` = 0,
    `is_comparable` = 0
");