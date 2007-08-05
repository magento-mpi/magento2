<?php

$conn->multi_query(<<<EOT

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


alter table `eav_entity_type` change `is_data_sharing` `is_data_sharing` tinyint (4) UNSIGNED  DEFAULT '1' NOT NULL ;

alter table `eav_entity_type` add column `increment_model` varchar (255)  NOT NULL  after `default_attribute_set_id`;
alter table `eav_entity_type` add column `increment_per_store` tinyint (1)UNSIGNED  DEFAULT '0' NOT NULL  after `increment_model`;
alter table `eav_entity_type` add column `increment_pad_length` tinyint (8) UNSIGNED  DEFAULT '8' NOT NULL after `increment_per_store`;
alter table `eav_entity_type` add column `increment_pad_char` char (1) DEFAULT '0' NOT NULL  after `increment_pad_length`;


alter table `eav_entity` add column `attribute_set_id` smallint (5)UNSIGNED   NOT NULL  after `entity_type_id`;
alter table `eav_entity` add column `increment_id` varchar (50) NOT NULL  after `attribute_set_id`;
alter table `eav_entity` add column `parent_id` int (11)UNSIGNED   NOT NULL  after `increment_id`;


drop table if exists `eav_entity_store`;
CREATE TABLE `eav_entity_store` (
  `entity_store_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `increment_prefix` varchar(20) NOT NULL default '',
  `increment_last_id` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`entity_store_id`),
  CONSTRAINT `FK_eav_entity_store_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_eav_entity_store_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

EOT
);