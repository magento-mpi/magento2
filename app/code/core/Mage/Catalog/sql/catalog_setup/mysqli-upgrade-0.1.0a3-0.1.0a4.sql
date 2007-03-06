alter table `pepper`.`catalog_attribute` drop foreign key `FK_ATTRIBUTE_TYPE` 
alter table `pepper`.`catalog_attribute` drop foreign key `FK_ATTRIBUTE_SOURCE` 

DROP TABLE IF EXISTS `catalog_attribute_source`;
DROP TABLE IF EXISTS `catalog_attribute_type`;

alter table `pepper`.`catalog_attribute` 
	change `attribute_source_id` `attribute_entity_type` varchar (20)  NOT NULL  COLLATE latin1_swedish_ci,
	change `attribute_type_id` `attribute_input_method` varchar (20)  NOT NULL  COLLATE latin1_swedish_ci ;

create table catalog_attribute_property (
property_id int unsigned not null auto_increment primary key,
attribute_id int unsigned not null,
property_type_id int unsigned not null,
property_value text,
key FK_CATALOG_ATTRIBUTE (attribute_id),
constraint FK_CATALOG_ATTRIBUTE foreign key (attribute_id) references catalog_attribute (attribute_id) on delete cascade on update cascade
);