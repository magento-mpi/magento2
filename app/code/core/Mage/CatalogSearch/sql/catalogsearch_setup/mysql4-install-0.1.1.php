<?php

$this->startSetup();

$this->run(<<<EOT

DROP TABLE IF EXISTS `catalogsearch`;

CREATE TABLE `catalogsearch` (
  `search_id` int(10) unsigned NOT NULL auto_increment,
  `search_query` varchar(255) NOT NULL default '',
  `num_results` int(10) unsigned NOT NULL default '0',
  `popularity` int(10) unsigned NOT NULL default '0',
  `redirect` varchar(255) NOT NULL default '',
  `synonims` text NOT NULL,
  PRIMARY KEY  (`search_id`),
  KEY `search_query` (`search_query`,`popularity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


EOT
);

$this->endSetup();