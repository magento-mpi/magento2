
drop table if exists `directory_country_postcode`;
CREATE TABLE `directory_country_postcode` (
  `country_id` smallint(6) unsigned NOT NULL default '0',
  `postcode` varchar(16) NOT NULL default '',
  `region_id` int(10) unsigned NOT NULL default '0',
  `county` varchar(50) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `postcode_class` char(1) NOT NULL default '',
  PRIMARY KEY  (`country_id`,`postcode`),
  KEY `country_id_2` (`country_id`,`region_id`),
  KEY `country_id_3` (`country_id`,`city`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
