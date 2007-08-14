<?php
$conn->dropForeignKey('eav_entity_attribute', 'FK_EAV_ENTITY_ATTRIVUTE_ATTRIBUTE');
$conn->dropForeignKey('eav_entity_attribute', 'FK_EAV_ENTITY_ATTRIVUTE_GROUP');

$conn->multi_query(<<<EOT
delete from eav_attribute_set where entity_type_id NOT IN (select entity_type_id from eav_entity_type);
delete from eav_entity_attribute where attribute_set_id NOT IN (select attribute_set_id from eav_attribute_set);

alter table `eav_entity_attribute` 
    ,add constraint `FK_EAV_ENTITY_ATTRIVUTE_ATTRIBUTE` foreign key(`attribute_id`) references `eav_attribute` (`attribute_id`) on delete cascade  on update cascade
    ,add constraint `FK_EAV_ENTITY_ATTRIVUTE_GROUP` foreign key(`attribute_group_id`) references `eav_attribute_group` (`attribute_group_id`) on delete cascade  on update cascade;

EOT
);