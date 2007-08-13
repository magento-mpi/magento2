<?php

$conn->multi_query(<<<EOT

DROP TABLE IF EXISTS `usa_postcode`;

CREATE TABLE `usa_postcode` (
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

EOT
);

$fp = fopen($sqlFilesDir.'/us_zipcodes.txt', 'r');
while ($row = fgets($fp)) {
	$conn->multi_query("insert into `usa_postcode` (country_id, postcode, region_id, county, city, postcode_class) values ".$row);
}
fclose($fp);