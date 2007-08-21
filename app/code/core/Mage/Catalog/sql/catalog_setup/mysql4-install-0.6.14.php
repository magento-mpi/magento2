<?php

$this->startSetup();

$this->createEntityTables('catalog_category_entity');

$this->createEntityTables('catalog_product_entity');

$this->run(<<<EOT

/*Table structure for table `catalog_category_tree` */

DROP TABLE IF EXISTS `catalog_category_tree`;

CREATE TABLE `catalog_category_tree` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned default '0',
  `left_key` int(10) unsigned default '0',
  `right_key` int(10) unsigned default '0',
  `level` smallint(4) unsigned NOT NULL default '0',
  `order` smallint(6) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_CATEGORY_PARENT` (`pid`),
  KEY `IDX_ORDER` (`order`),
  KEY `IDX_LEVEL` (`level`),
  KEY `IDX_ORDER_LEVEL` (`level`,`order`),
  CONSTRAINT `FK_CATALOG_CATEGORY_TREE_PARENT` FOREIGN KEY (`pid`) REFERENCES `catalog_category_tree` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categories tree';

/*Data for the table `catalog_category_tree` */

ALTER TABLE `catalog_category_entity`
	ADD CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_TREE_NODE` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_tree` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
	
insert into `catalog_category_tree` (`entity_id`,`pid`,`left_key`,`right_key`,`level`,`order`) values (1,NULL,0,0,0,1),(158,1,0,0,1,1),(159,1,0,0,1,2),(160,1,0,0,1,3),(161,158,0,0,2,1),(162,158,0,0,2,2),(163,158,0,0,2,3),(164,158,0,0,2,4),(165,158,0,0,2,5),(166,161,0,0,3,1),(167,161,0,0,3,2),(168,161,0,0,3,3),(169,161,0,0,3,4),(170,161,0,0,3,5);

insert into `catalog_category_entity` (`entity_id`,`entity_type_id`,`attribute_set_id`,`parent_id`,`store_id`,`created_at`,`updated_at`,`is_active`) values (1,9,12,0,1,'2007-07-20 18:46:08','2007-08-07 09:50:15',1),(158,9,12,1,0,'2007-08-07 09:37:05','2007-08-07 10:34:46',1),(159,9,12,1,0,'2007-08-07 09:38:15','2007-08-07 10:04:22',1),(160,9,12,1,0,'2007-08-07 09:38:54','2007-08-07 09:50:11',1),(161,9,12,158,0,'2007-08-07 09:50:48','2007-08-07 10:34:46',1),(162,9,12,158,0,'2007-08-07 09:51:50','2007-08-07 10:22:31',1),(163,9,12,158,0,'2007-08-07 09:52:25','2007-08-07 10:22:31',1),(164,9,12,158,0,'2007-08-07 09:53:07','2007-08-07 10:22:31',1),(165,9,12,158,0,'2007-08-07 09:53:29','2007-08-07 10:22:31',1),(166,9,12,161,0,'2007-08-07 10:24:03','2007-08-07 10:24:03',1),(167,9,12,161,0,'2007-08-07 10:33:34','2007-08-07 10:33:34',1),(168,9,12,161,0,'2007-08-07 10:33:57','2007-08-07 10:33:57',1),(169,9,12,161,0,'2007-08-07 10:34:24','2007-08-07 10:34:24',1),(170,9,12,161,0,'2007-08-07 10:34:46','2007-08-07 10:34:46',1);

insert into `catalog_category_entity_int` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (38,9,120,0,166,1),(39,9,120,1,166,1);

insert into `catalog_category_entity_text` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (625,9,121,0,158,'158,161,166,167,168,169,170,162,163,164,165'),(628,9,121,0,159,'159'),(629,9,122,0,159,'159'),(631,9,121,0,160,'160'),(632,9,122,0,160,'160'),(633,9,123,0,160,''),(634,9,123,0,159,''),(641,9,121,0,1,'1,158,159,160'),(646,9,122,0,1,''),(651,9,123,0,1,'158,159,160'),(657,9,121,0,161,'161,166,167,168,169,170'),(658,9,122,0,161,'161'),(660,9,123,0,158,'161,162,163,164,165'),(661,9,121,0,162,'162'),(662,9,122,0,162,'162'),(663,9,123,0,162,''),(664,9,121,0,163,'163'),(665,9,122,0,163,'163'),(666,9,123,0,163,''),(667,9,121,0,164,'164'),(668,9,122,0,164,'164'),(669,9,123,0,164,''),(670,9,121,0,165,'165'),(671,9,122,0,165,'165'),(672,9,123,0,165,''),(692,9,121,1,158,'158,161,166,167,168,169,170,162,163,164,165'),(694,9,123,1,158,'161,162,163,164,165'),(695,9,121,1,161,'161,166,167,168,169,170'),(696,9,122,1,161,'161'),(697,9,123,1,161,'166,167,168,169,170'),(699,9,121,1,162,'162'),(700,9,122,1,162,'162'),(701,9,123,1,162,''),(703,9,121,1,163,'163'),(704,9,122,1,163,'163'),(705,9,123,1,163,''),(707,9,121,1,164,'164'),(708,9,122,1,164,'164'),(709,9,123,1,164,''),(711,9,121,1,165,'165'),(712,9,122,1,165,'165'),(713,9,123,1,165,''),(714,9,122,1,158,''),(715,9,121,0,166,'166'),(716,9,121,1,166,'166'),(717,9,122,0,166,'166,161'),(718,9,122,1,166,'166,161'),(719,9,123,0,166,''),(720,9,123,1,166,'');
insert into `catalog_category_entity_text` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (721,9,123,0,161,'166,167,168,169,170'),(724,9,121,0,167,'167'),(725,9,121,1,167,'167'),(726,9,122,0,167,'167,161'),(727,9,122,1,167,'167,161'),(728,9,123,0,167,''),(729,9,123,1,167,''),(732,9,121,0,168,'168'),(733,9,121,1,168,'168'),(734,9,122,0,168,'168,161'),(735,9,122,1,168,'168,161'),(736,9,123,0,168,''),(737,9,123,1,168,''),(740,9,121,0,169,'169'),(741,9,121,1,169,'169'),(742,9,122,0,169,'169,161'),(743,9,122,1,169,'169,161'),(744,9,123,0,169,''),(745,9,123,1,169,''),(748,9,121,0,170,'170'),(749,9,121,1,170,'170'),(750,9,122,0,170,'170,161'),(751,9,122,1,170,'170,161'),(752,9,123,0,170,''),(753,9,123,1,170,''),(755,9,122,0,158,'');

insert into `catalog_category_entity_varchar` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (1,9,111,0,1,'ROOT'),(490,9,111,0,158,'Base Store Root'),(491,9,118,0,158,'PRODUCTS'),(492,9,111,0,159,'Summer Store'),(493,9,118,0,159,'PRODUCTS'),(494,9,111,0,160,'Winter Store'),(495,9,118,0,160,'PRODUCTS'),(496,9,111,0,161,'Apparel'),(497,9,118,0,161,'PRODUCTS'),(498,9,111,0,162,'Electronics'),(499,9,118,0,162,'PRODUCTS'),(500,9,111,0,163,'Books'),(501,9,118,0,163,'PRODUCTS'),(502,9,111,0,164,'Entertainment'),(503,9,118,0,164,'PRODUCTS'),(504,9,111,0,165,'Garden'),(505,9,118,0,165,'PRODUCTS'),(520,9,111,1,158,'Base Store Root'),(521,9,118,1,158,'PRODUCTS'),(522,9,111,1,161,'Apparel'),(523,9,118,1,161,'PRODUCTS'),(524,9,111,1,162,'Electronics'),(525,9,118,1,162,'PRODUCTS'),(526,9,111,1,163,'Books'),(527,9,118,1,163,'PRODUCTS'),(528,9,111,1,164,'Entertainment'),(529,9,118,1,164,'PRODUCTS'),(530,9,111,1,165,'Garden'),(531,9,118,1,165,'PRODUCTS'),(532,9,111,0,166,'Accessories'),(533,9,111,1,166,'Accessories'),(534,9,118,0,166,'PRODUCTS'),(535,9,118,1,166,'PRODUCTS'),(536,9,111,0,167,'Bags'),(537,9,111,1,167,'Bags'),(538,9,118,0,167,'PRODUCTS'),(539,9,118,1,167,'PRODUCTS'),(540,9,111,0,168,'Shoes'),(541,9,111,1,168,'Shoes'),(542,9,118,0,168,'PRODUCTS'),(543,9,118,1,168,'PRODUCTS'),(544,9,111,0,169,'Mittens'),(545,9,111,1,169,'Mittens'),(546,9,118,0,169,'PRODUCTS'),(547,9,118,1,169,'PRODUCTS');
insert into `catalog_category_entity_varchar` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (548,9,111,0,170,'Gloves'),(549,9,111,1,170,'Gloves'),(550,9,118,0,170,'PRODUCTS'),(551,9,118,1,170,'PRODUCTS');



DROP TABLE IF EXISTS `catalog_category_product`;

CREATE TABLE `catalog_category_product` (
  `category_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `position` int(10) unsigned NOT NULL default '0',
  KEY `CATALOG_CATEGORY_PRODUCT_CATEGORY` (`category_id`),
  KEY `CATALOG_CATEGORY_PRODUCT_PRODUCT` (`product_id`),
  CONSTRAINT `CATALOG_CATEGORY_PRODUCT_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CATALOG_CATEGORY_PRODUCT_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_product` */

/*Table structure for table `catalog_compare_item` */

DROP TABLE IF EXISTS `catalog_compare_item`;

CREATE TABLE `catalog_compare_item` (
  `catalog_compare_item_id` int(11) unsigned NOT NULL auto_increment,
  `visitor_id` int(11) unsigned NOT NULL default '0',
  `customer_id` int(11) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`catalog_compare_item_id`),
  KEY `FK_CATALOG_COMPARE_ITEM_CUSTOMER` (`customer_id`),
  KEY `FK_CATALOG_COMPARE_ITEM_PRODUCT` (`product_id`),
  CONSTRAINT `FK_CATALOG_COMPARE_ITEM_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_compare_item` */

/*Table structure for table `catalog_product_bundle_option` */

DROP TABLE IF EXISTS `catalog_product_bundle_option`;

CREATE TABLE `catalog_product_bundle_option` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`option_id`),
  KEY `FK_catalog_product_bundle_option` (`product_id`),
  CONSTRAINT `FK_catalog_product_bundle_option` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_bundle_option` */

/*Table structure for table `catalog_product_bundle_option_link` */

DROP TABLE IF EXISTS `catalog_product_bundle_option_link`;

CREATE TABLE `catalog_product_bundle_option_link` (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `discount` decimal(10,4) unsigned default NULL,
  PRIMARY KEY  (`link_id`),
  KEY `FK_catalog_product_bundle_option_link` (`option_id`),
  KEY `FK_catalog_product_bundle_option_link_entity` (`product_id`),
  CONSTRAINT `FK_catalog_product_bundle_option_link_entity` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_catalog_product_bundle_option_link` FOREIGN KEY (`option_id`) REFERENCES `catalog_product_bundle_option` (`option_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_bundle_option_link` */

/*Table structure for table `catalog_product_bundle_option_value` */

DROP TABLE IF EXISTS `catalog_product_bundle_option_value`;

CREATE TABLE `catalog_product_bundle_option_value` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `option_id` int(10) unsigned NOT NULL default '0',
  `label` varchar(255) default NULL,
  `position` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_catalog_product_bundle_option_label` (`option_id`),
  CONSTRAINT `FK_catalog_product_bundle_option_label` FOREIGN KEY (`option_id`) REFERENCES `catalog_product_bundle_option` (`option_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_bundle_option_value` */

/*Table structure for table `catalog_product_entity_gallery` */

DROP TABLE IF EXISTS `catalog_product_entity_gallery`;

CREATE TABLE `catalog_product_entity_gallery` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `position` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `IDX_BASE` USING BTREE (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_GALLERY_ENTITY` (`entity_id`),
  KEY `FK_CATALOG_CATEGORY_ENTITY_GALLERY_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CATALOG_CATEGORY_ENTITY_GALLERY_STORE` (`store_id`),
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_GALLERY_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_GALLERY_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_GALLERY_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_entity_gallery` */

/*Table structure for table `catalog_product_entity_tier_price` */

DROP TABLE IF EXISTS `catalog_product_entity_tier_price`;

CREATE TABLE `catalog_product_entity_tier_price` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `qty` smallint(5) unsigned NOT NULL default '1',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_STORE` (`store_id`),
  KEY `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_PRODUCT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_PRODUCT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_entity_tier_price` */

/*Table structure for table `catalog_product_link` */

DROP TABLE IF EXISTS `catalog_product_link`;

CREATE TABLE `catalog_product_link` (
  `link_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `linked_product_id` int(10) unsigned NOT NULL default '0',
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `FK_LINK_PRODUCT` (`product_id`),
  KEY `FK_LINKED_PRODUCT` (`linked_product_id`),
  KEY `FK_PRODUCT_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_PRODUCT_LINK_LINKED_PRODUCT` FOREIGN KEY (`linked_product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_LINK_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES `catalog_product_link_type` (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Related products';

/*Data for the table `catalog_product_link` */

/*Table structure for table `catalog_product_link_attribute` */

DROP TABLE IF EXISTS `catalog_product_link_attribute`;

CREATE TABLE `catalog_product_link_attribute` (
  `product_link_attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  `product_link_attribute_code` varchar(32) NOT NULL default '',
  `data_type` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`product_link_attribute_id`),
  KEY `FK_ATTRIBUTE_PRODUCT_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_PRODUCT_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES `catalog_product_link_type` (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Attributes for product link';

/*Data for the table `catalog_product_link_attribute` */

insert  into `catalog_product_link_attribute`(`product_link_attribute_id`,`link_type_id`,`product_link_attribute_code`,`data_type`) values (1,2,'qty','decimal'),(2,1,'position','int'),(3,4,'position','int'),(4,5,'position','int'),(6,1,'qty','decimal'),(7,3,'position','int'),(8,3,'qty','decimal');

/*Table structure for table `catalog_product_link_attribute_decimal` */

DROP TABLE IF EXISTS `catalog_product_link_attribute_decimal`;

CREATE TABLE `catalog_product_link_attribute_decimal` (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `product_link_attribute_id` smallint(6) unsigned default NULL,
  `link_id` int(11) unsigned default NULL,
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_DECIMAL_PRODUCT_LINK_ATTRIBUTE` (`product_link_attribute_id`),
  KEY `FK_DECIMAL_LINK` (`link_id`),
  CONSTRAINT `FK_DECIMAL_LINK` FOREIGN KEY (`link_id`) REFERENCES `catalog_product_link` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_DECIMAL_PRODUCT_LINK_ATTRIBUTE` FOREIGN KEY (`product_link_attribute_id`) REFERENCES `catalog_product_link_attribute` (`product_link_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Decimal attributes values';

/*Data for the table `catalog_product_link_attribute_decimal` */

/*Table structure for table `catalog_product_link_attribute_int` */

DROP TABLE IF EXISTS `catalog_product_link_attribute_int`;

CREATE TABLE `catalog_product_link_attribute_int` (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `product_link_attribute_id` smallint(6) unsigned default NULL,
  `link_id` int(11) unsigned default NULL,
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_INT_PRODUCT_LINK_ATTRIBUTE` (`product_link_attribute_id`),
  KEY `FK_INT_PRODUCT_LINK` (`link_id`),
  CONSTRAINT `FK_INT_PRODUCT_LINK_ATTRIBUTE` FOREIGN KEY (`product_link_attribute_id`) REFERENCES `catalog_product_link_attribute` (`product_link_attribute_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_INT_PRODUCT_LINK` FOREIGN KEY (`link_id`) REFERENCES `catalog_product_link` (`link_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_link_attribute_int` */

/*Table structure for table `catalog_product_link_attribute_varchar` */

DROP TABLE IF EXISTS `catalog_product_link_attribute_varchar`;

CREATE TABLE `catalog_product_link_attribute_varchar` (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `product_link_attribute_id` smallint(6) unsigned NOT NULL default '0',
  `link_id` int(11) unsigned default NULL,
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_VARCHAR_PRODUCT_LINK_ATTRIBUTE` (`product_link_attribute_id`),
  KEY `FK_VARCHAR_LINK` (`link_id`),
  CONSTRAINT `FK_VARCHAR_LINK` FOREIGN KEY (`link_id`) REFERENCES `catalog_product_link` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_VARCHAR_PRODUCT_LINK_ATTRIBUTE` FOREIGN KEY (`product_link_attribute_id`) REFERENCES `catalog_product_link_attribute` (`product_link_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Varchar attributes values';

/*Data for the table `catalog_product_link_attribute_varchar` */

/*Table structure for table `catalog_product_link_type` */

DROP TABLE IF EXISTS `catalog_product_link_type`;

CREATE TABLE `catalog_product_link_type` (
  `link_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`link_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Types of product link(Related, superproduct, bundles)';

/*Data for the table `catalog_product_link_type` */

insert  into `catalog_product_link_type`(`link_type_id`,`code`) values (1,'relation'),(2,'bundle'),(3,'super'),(4,'up_sell'),(5,'cross_sell');

/*Table structure for table `catalog_product_status` */

DROP TABLE IF EXISTS `catalog_product_status`;

CREATE TABLE `catalog_product_status` (
  `status_id` tinyint(3) unsigned NOT NULL auto_increment,
  `status_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available product statuses';

/*Data for the table `catalog_product_status` */

insert  into `catalog_product_status`(`status_id`,`status_code`) values (1,'Enabled'),(2,'Disabled'),(3,'Out-of-stock');

/*Table structure for table `catalog_product_store` */

DROP TABLE IF EXISTS `catalog_product_store`;

CREATE TABLE `catalog_product_store` (
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `IDX_PS_UNIQ` (`store_id`,`product_id`),
  KEY `store_id` (`store_id`),
  KEY `FK_CATALOG_PRDUCT_STORE_PRODUCT` (`product_id`),
  CONSTRAINT `FK_CATALOG_PRDUCT_STORE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRDUCT_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_store` */

/*Table structure for table `catalog_product_super_attribute` */

DROP TABLE IF EXISTS `catalog_product_super_attribute`;

CREATE TABLE `catalog_product_super_attribute` (
  `product_super_attribute_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `position` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`product_super_attribute_id`),
  KEY `FK_SUPER_PRODUCT_ATTRIBUTE_PRODUCT` (`product_id`),
  CONSTRAINT `FK_SUPER_PRODUCT_ATTRIBUTE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_super_attribute` */

/*Table structure for table `catalog_product_super_attribute_label` */

DROP TABLE IF EXISTS `catalog_product_super_attribute_label`;

CREATE TABLE `catalog_product_super_attribute_label` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `product_super_attribute_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `value` varchar(255) character set utf8 NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SUPER_PRODUCT_ATTRIBUTE_LABEL` (`product_super_attribute_id`),
  CONSTRAINT `FK_SUPER_PRODUCT_ATTRIBUTE_LABEL` FOREIGN KEY (`product_super_attribute_id`) REFERENCES `catalog_product_super_attribute` (`product_super_attribute_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `catalog_product_super_attribute_label` */

/*Table structure for table `catalog_product_super_attribute_pricing` */

DROP TABLE IF EXISTS `catalog_product_super_attribute_pricing`;

CREATE TABLE `catalog_product_super_attribute_pricing` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `product_super_attribute_id` int(10) unsigned NOT NULL default '0',
  `value_index` varchar(255) character set utf8 NOT NULL default '',
  `is_percent` tinyint(1) unsigned default '0',
  `pricing_value` decimal(10,4) default NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_SUPER_PRODUCT_ATTRIBUTE_PRICING` (`product_super_attribute_id`),
  CONSTRAINT `FK_SUPER_PRODUCT_ATTRIBUTE_PRICING` FOREIGN KEY (`product_super_attribute_id`) REFERENCES `catalog_product_super_attribute` (`product_super_attribute_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_super_attribute_pricing` */

/*Table structure for table `catalog_product_super_link` */

DROP TABLE IF EXISTS `catalog_product_super_link`;

CREATE TABLE `catalog_product_super_link` (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `FK_SUPER_PRODUCT_LINK_PARENT` (`parent_id`),
  KEY `FK_catalog_product_super_link` (`product_id`),
  CONSTRAINT `FK_SUPER_PRODUCT_LINK_ENTITY` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_SUPER_PRODUCT_LINK_PARENT` FOREIGN KEY (`parent_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_super_link` */

/*Table structure for table `catalog_product_type` */

DROP TABLE IF EXISTS `catalog_product_type`;

CREATE TABLE `catalog_product_type` (
  `type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `code` varchar(32) character set cp1251 NOT NULL default '',
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_type` */

insert  into `catalog_product_type`(`type_id`,`code`) values (1,'Simple Product'),(2,'bundle'),(3,'Configurable Super Product'),(4,'Grouped Super Product');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

EOT
);

$this->run(<<<EOT

insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'catalog','Catalog','text','','','','',40,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'catalog/category','Category','text','','','','',1,0,0,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'catalog/category/root_id','Root category','select','','','adminhtml/system_config_backend_category','adminhtml/system_config_source_category',1,0,0,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'catalog/frontend','Frontend','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'catalog/frontend/product_per_page','Product per page','text','required-entry validate-number','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'catalog/images','Images Configuration','text','','','','',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'catalog/images/category_upload_path','Category upload directory','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'catalog/images/category_upload_url','Category upload url','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'catalog/images/product_upload_path','Product upload directory','text','','','','',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'catalog/images/product_upload_url','Product upload url','text','','','','',4,1,1,1,'');

insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'catalog/images/category_upload_path','{{root_dir}}/media/catalog/category/','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'catalog/images/category_upload_url','{{base_path}}media/catalog/category/','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'catalog/images/product_upload_path','{{root_dir}}/media/catalog/product/','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'catalog/images/product_upload_url','{{base_path}}media/catalog/product/','',0);


EOT
);

$this->endSetup();

$this->installEntities($this->getDefaultEntities());