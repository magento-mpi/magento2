drop table if exists `eav_entity_datetime`;
CREATE TABLE `eav_entity_datetime` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_DATETIME_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY` (`entity_id`),
  CONSTRAINT `FK_EAV_ENTITY_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `eav_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EAV_ENTITY_DATETIME_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EAV_ENTITY_DATETIME_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datetime values of attributes';

alter table `eav_entity_datetime` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `eav_entity_datetime` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `eav_entity_decimal` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `eav_entity_decimal` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `eav_entity_int` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `eav_entity_int` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `eav_entity_varchar` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `eav_entity_varchar` add index `value_by_entity_type` (`entity_type_id`, `value`);