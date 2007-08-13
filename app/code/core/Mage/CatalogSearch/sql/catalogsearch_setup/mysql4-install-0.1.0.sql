
/*Table structure for table `catalog_search` */

DROP TABLE IF EXISTS `catalog_search`;
DROP TABLE IF EXISTS `catalogsearch`;

CREATE TABLE `catalogsearch` (
  `search_id` int(10) unsigned NOT NULL auto_increment,
  `search_query` varchar(255) NOT NULL default '',
  `num_results` int(10) unsigned NOT NULL default '0',
  `popularity` int(10) unsigned NOT NULL default '0',
  `redirect` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`search_id`),
  KEY `search_query` (`search_query`,`popularity`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8;