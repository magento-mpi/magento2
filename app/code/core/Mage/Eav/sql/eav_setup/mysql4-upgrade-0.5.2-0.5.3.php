<?php

$conn->multi_query(<<<EOT

alter table `magento_moshe`.`eav_entity` ,change `entity_type_id` `entity_type_id` smallint (8)UNSIGNED  DEFAULT '0' NOT NULL 
alter table `magento_moshe`.`eav_entity_datetime` ,change `entity_type_id` `entity_type_id` smallint (8)UNSIGNED  DEFAULT '0' NOT NULL 

EOT
);