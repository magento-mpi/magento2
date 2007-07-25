<?php

$conn->multi_query(<<<EOT

alter table `sales_order_entity_datetime` ,change `entity_type_id` `entity_type_id` smallint (8)UNSIGNED  DEFAULT '0' NOT NULL ;
alter table `sales_order_entity_decimal` ,change `entity_type_id` `entity_type_id` smallint (8)UNSIGNED  DEFAULT '0' NOT NULL ;
alter table `sales_order_entity_int` ,change `entity_type_id` `entity_type_id` smallint (8)UNSIGNED  DEFAULT '0' NOT NULL ;
alter table `sales_order_entity_text` ,change `entity_type_id` `entity_type_id` smallint (8)UNSIGNED  DEFAULT '0' NOT NULL ;
alter table `sales_order_entity_varchar` ,change `entity_type_id` `entity_type_id` smallint (8)UNSIGNED  DEFAULT '0' NOT NULL ;

EOT
);