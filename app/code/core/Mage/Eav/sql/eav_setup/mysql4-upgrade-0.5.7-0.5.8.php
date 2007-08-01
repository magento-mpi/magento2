<?php

$conn->dropKey('eav_attribute', 'entity_type_id');

$conn->dropForeignKey('eav_attribute', 'FK_eav_attribute');
$conn->dropForeignKey('eav_attribute_group', 'FK_eav_attribute_group');
$conn->dropForeignKey('eav_attribute_set', 'FK_eav_attribute_set');
$conn->dropForeignKey('eav_entity', 'FK_eav_entity');
$conn->dropForeignKey('eav_entity', 'FK_eav_entity_store');
$conn->dropForeignKey('eav_entity_attribute', 'FK_eav_entity_attribute');
$conn->dropForeignKey('eav_entity_attribute', 'FK_eav_entity_attribute_group');

$conn->multi_query(<<<EOT
alter table `eav_entity_type` 
    ,change `entity_name` `entity_type_code` varchar (50)  NOT NULL  COLLATE utf8_general_ci
; 

delete from eav_attribute_set
where entity_type_id not in (select entity_type_id from eav_entity_type)
;

delete from eav_attribute_group
where attribute_set_id not in (select attribute_set_id from eav_attribute_set)
;

delete from eav_attribute
where entity_type_id not in (select entity_type_id from eav_entity_type)
;

delete from eav_entity_attribute 
where attribute_id not in (select attribute_id from eav_attribute)
    or entity_type_id not in (select entity_type_id from eav_entity_type)
    or attribute_set_id not in (select attribute_set_id from eav_attribute_set)
    or attribute_group_id not in (select attribute_group_id from eav_attribute_group)
;

alter table `eav_attribute`
    ,add unique `entity_type_id` (`entity_type_id`, `attribute_code`)
    ,add constraint `FK_eav_attribute` foreign key(`entity_type_id`) references `eav_entity_type` (`entity_type_id`) on delete cascade  on update cascade
; 

alter table `eav_attribute_group`
    ,add constraint `FK_eav_attribute_group` foreign key(`attribute_set_id`) references `eav_attribute_set` (`attribute_set_id`) on delete cascade  on update cascade
; 

alter table `eav_attribute_set` 
    ,add constraint `FK_eav_attribute_set` foreign key(`entity_type_id`) references `eav_entity_type` (`entity_type_id`) on delete cascade  on update cascade
; 

alter table `eav_entity` 
    ,change `entity_type_id` `entity_type_id` smallint (8)UNSIGNED  DEFAULT '0' NOT NULL 
    ,add constraint `FK_eav_entity` foreign key(`entity_type_id`) references `eav_entity_type` (`entity_type_id`) on delete cascade  on update cascade
    ,add constraint `FK_eav_entity_store` foreign key(`store_id`)references `core_store` (`store_id`) on delete cascade  on update cascade
;

alter table `eav_entity_attribute` 
    ,add constraint `FK_eav_entity_attribute` foreign key(`attribute_id`) references `eav_attribute` (`attribute_id`) on delete cascade  on update cascade
    ,add constraint `FK_eav_entity_attribute_group` foreign key(`attribute_group_id`) references `eav_attribute_group` (`attribute_group_id`) on delete cascade  on update cascade 
;
EOT
);