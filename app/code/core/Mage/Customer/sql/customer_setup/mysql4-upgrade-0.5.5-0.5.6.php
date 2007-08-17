<?php

$conn->multi_query(<<<EOT
UPDATE `eav_entity_type` SET `increment_model`='eav/entity_increment_numeric' WHERE `entity_type_code`='customer';
ALTER TABLE `customer_entity` ADD `increment_id` VARCHAR(50) NOT NULL AFTER `attribute_set_id`;
EOT
);

Mage::getSingleton('core/store')->updateDatasharing();