
UPDATE `eav_attribute` SET `frontend_label` = 'Main Image' WHERE `attribute_code` = 'image' and `entity_type_id`=10;
UPDATE `eav_attribute` SET `frontend_label` = 'Thumbnail Image' WHERE `attribute_code` = 'small_image';
UPDATE `eav_attribute` SET `frontend_label` = 'Cost<br/>(For internal use)' WHERE `attribute_code` = 'cost';
UPDATE `eav_attribute` SET `frontend_label` = 'SEF URL Identifier<br/>(will replace product name)' WHERE `attribute_code` = 'url_key';
UPDATE `eav_attribute` SET `frontend_label` = 'Qty Uses Decimals' WHERE `attribute_code` = 'qty_is_decimal';

UPDATE `catalog_product_type` SET `code` = 'Configurable Product' WHERE `type_id` =3;
UPDATE `catalog_product_type` SET `code` = 'Grouped Product' WHERE `type_id` =4;
