<?php

$this->startSetup()->run("

DROP TABLE IF EXISTS `core_layout`;

DROP TABLE IF EXISTS `core_layout_update`;
CREATE TABLE `core_layout_update` (
  `layout_update_id` int(10) unsigned NOT NULL auto_increment,
  `handle` varchar(255) default NULL,
  `xml` text,
  PRIMARY KEY  (`layout_update_id`),
  KEY `handle` (`handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `core_layout_link`;
CREATE TABLE `core_layout_link` (
  `layout_link_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `package` varchar(64) NOT NULL default '',
  `theme` varchar(64) NOT NULL default '',
  `layout_update_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`layout_link_id`),
  UNIQUE KEY `store_id` (`store_id`,`package`,`theme`,`layout_update_id`),
  KEY `FK_core_layout_link_update` (`layout_update_id`),
  CONSTRAINT `FK_core_layout_link_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_core_layout_link_update` FOREIGN KEY (`layout_update_id`) REFERENCES `core_layout_update` (`layout_update_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

")->endSetup();