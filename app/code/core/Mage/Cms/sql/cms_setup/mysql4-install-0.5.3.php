<?php

$this->startSetup()->run(<<<EOT


DROP TABLE IF EXISTS `cms_block`;

CREATE TABLE `cms_block` (
  `block_id` smallint(6) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `identifier` varchar(255) NOT NULL default '',
  `content` text,
  `creation_time` datetime default NULL,
  `update_time` datetime default NULL,
  `is_active` tinyint(1) NOT NULL default '1',
  `store_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS Blocks';

/*Data for the table `cms_block` */

/*Table structure for table `cms_page` */

DROP TABLE IF EXISTS `cms_page`;

CREATE TABLE `cms_page` (
  `page_id` smallint(6) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `root_template` varchar(255) NOT NULL default '',
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `identifier` varchar(100) NOT NULL default '',
  `content` text,
  `creation_time` datetime default NULL,
  `update_time` datetime default NULL,
  `is_active` tinyint(1) NOT NULL default '1',
  `store_id` tinyint(4) NOT NULL default '1',
  `sort_order` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS pages';

/*Data for the table `cms_page` */

insert  into `cms_page`(`page_id`,`title`,`root_template`,`meta_keywords`,`meta_description`,`identifier`,`content`,`creation_time`,`update_time`,`is_active`,`store_id`,`sort_order`) values (1,'404 Not Found 1','','Page keywords','Page description','no-route','<h1 class=\"page-heading\">404 Error</h1>\r\n<p>\r\nPage not found.<br />\r\n<em>by NoRoute Action :-)</em>\r\n</p>\r\n','2007-06-20 18:38:32','2007-07-27 10:45:35',1,0,0);

EOT
)->endSetup();