alter table `eav_entity_datetime` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `eav_entity_datetime` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `eav_entity_decimal` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `eav_entity_decimal` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `eav_entity_int` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `eav_entity_int` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `eav_entity_varchar` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `eav_entity_varchar` add index `value_by_entity_type` (`entity_type_id`, `value`);