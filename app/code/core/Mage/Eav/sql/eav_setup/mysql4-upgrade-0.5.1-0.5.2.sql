/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.22 : Database - magento_dmitriy
*********************************************************************
Server version : 4.1.22
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `eav_attribute` */

DROP TABLE IF EXISTS `eav_attribute`;

CREATE TABLE `eav_attribute` (
  `attribute_id` smallint(5) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_name` varchar(50) character set utf8 NOT NULL default '',
  `attribute_model` varchar(255) character set utf8 default NULL,
  `backend_model` varchar(255) character set utf8 default NULL,
  `backend_type` enum('static','datetime','decimal','int','text','varchar') character set utf8 NOT NULL default 'static',
  `backend_table` varchar(255) character set utf8 default NULL,
  `frontend_model` varchar(255) character set utf8 default NULL,
  `frontend_input` varchar(50) character set utf8 default NULL,
  `frontend_label` varchar(255) character set utf8 default NULL,
  `frontend_class` varchar(255) character set utf8 default NULL,
  `source_model` varchar(255) character set utf8 default NULL,
  `is_global` tinyint(1) unsigned NOT NULL default '1',
  `is_visible` tinyint(1) unsigned NOT NULL default '1',
  `is_required` tinyint(1) unsigned NOT NULL default '0',
  `is_user_defined` tinyint(1) unsigned NOT NULL default '0',
  `default_value` text character set utf8,
  `is_searchable` tinyint(1) unsigned NOT NULL default '0',
  `is_filterable` tinyint(1) unsigned NOT NULL default '0',
  `is_comparable` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`),
  UNIQUE KEY `entity_type_id` (`entity_type_id`,`attribute_name`)
) ENGINE=MyISAM AUTO_INCREMENT=271 DEFAULT CHARSET=latin1;

/*Data for the table `eav_attribute` */

insert into `eav_attribute` (`attribute_id`,`entity_type_id`,`attribute_name`,`attribute_model`,`backend_model`,`backend_type`,`backend_table`,`frontend_model`,`frontend_input`,`frontend_label`,`frontend_class`,`source_model`,`is_global`,`is_visible`,`is_required`,`is_user_defined`,`default_value`,`is_searchable`,`is_filterable`,`is_comparable`) values (1,1,'firstname',NULL,'','varchar','','','text','First Name','','',1,1,1,0,NULL,0,0,0),(2,1,'lastname',NULL,'','varchar','','','text','Last Name','','',1,1,1,0,NULL,0,0,0),(3,1,'email',NULL,'','varchar','','','text','Email','validate-email','',1,1,1,0,NULL,0,0,0),(4,1,'password_hash',NULL,'customer_entity/customer_attribute_backend_password','varchar','','','','','','',1,1,0,0,NULL,0,0,0),(5,1,'customer_group',NULL,'','int','','','select','Customer Group','','customer_entity/customer_attribute_source_group',1,1,1,0,NULL,0,0,0),(6,1,'store_balance',NULL,'','decimal','','','text','Balance','validate-number','',1,1,1,0,NULL,0,0,0),(7,1,'default_billing',NULL,'customer_entity/customer_attribute_backend_billing','int','','','','','','',1,0,0,0,NULL,0,0,0),(8,1,'default_shipping',NULL,'customer_entity/customer_attribute_backend_shipping','int','','','','','','',1,0,0,0,NULL,0,0,0),(9,2,'firstname',NULL,'','varchar','','','text','First Name','','',1,1,1,0,NULL,0,0,0);
insert into `eav_attribute` (`attribute_id`,`entity_type_id`,`attribute_name`,`attribute_model`,`backend_model`,`backend_type`,`backend_table`,`frontend_model`,`frontend_input`,`frontend_label`,`frontend_class`,`source_model`,`is_global`,`is_visible`,`is_required`,`is_user_defined`,`default_value`,`is_searchable`,`is_filterable`,`is_comparable`) values (10,2,'lastname',NULL,'','varchar','','','text','Last Name','','',1,1,1,0,NULL,0,0,0),(11,2,'country_id',NULL,'','int','','','select','Country','countries input-text','customer_entity/address_attribute_source_country',1,1,1,0,NULL,0,0,0),(12,2,'region',NULL,'customer_entity/address_attribute_backend_region','varchar','','','text','State/Province','regions','',1,1,1,0,NULL,0,0,0),(13,2,'region_id',NULL,'','int','','','hidden','','','customer_entity/address_attribute_source_region',1,1,0,0,NULL,0,0,0),(14,2,'postcode',NULL,'','varchar','','','text','ZIP/Post Code','','',1,1,1,0,NULL,0,0,0),(15,2,'city',NULL,'','varchar','','','text','City','','',1,1,1,0,NULL,0,0,0),(16,2,'street',NULL,'customer_entity/address_attribute_backend_street','text','','','textarea','Street Address','','',1,1,1,0,NULL,0,0,0),(17,2,'telephone',NULL,'','varchar','','','text','Telephone','','',1,1,1,0,NULL,0,0,0),(18,2,'fax',NULL,'','varchar','','','text','Fax','','',1,1,0,0,NULL,0,0,0),(19,3,'method_type',NULL,'','int','','','select','Payment Method','','',1,1,1,0,NULL,0,0,0),(95,2,'company',NULL,NULL,'varchar',NULL,NULL,'text','Company',NULL,NULL,1,1,0,0,NULL,0,0,0),(96,10,'name',NULL,NULL,'varchar',NULL,NULL,'text','Name',NULL,NULL,1,1,1,0,NULL,0,0,0),(97,10,'description',NULL,NULL,'text',NULL,NULL,'textarea','Description',NULL,NULL,1,1,0,0,NULL,0,0,0),(98,10,'sku',NULL,NULL,'varchar',NULL,NULL,'text','SKU',NULL,NULL,1,1,1,0,NULL,0,0,0),(99,10,'price',NULL,NULL,'decimal',NULL,NULL,'text','Price',NULL,NULL,1,1,1,0,NULL,0,0,0),(100,10,'cost',NULL,NULL,'decimal',NULL,NULL,'text','Cost',NULL,NULL,1,1,1,0,NULL,0,0,0),(101,10,'weight',NULL,NULL,'decimal',NULL,NULL,'text','Weigth',NULL,NULL,1,1,1,0,NULL,0,0,0),(102,10,'manufacturer',NULL,NULL,'int',NULL,NULL,'select','Manufacturer',NULL,NULL,1,1,0,0,NULL,0,0,0),(103,10,'meta_title',NULL,NULL,'varchar',NULL,NULL,'text','Meta Title',NULL,NULL,1,1,0,0,NULL,0,0,0);
insert into `eav_attribute` (`attribute_id`,`entity_type_id`,`attribute_name`,`attribute_model`,`backend_model`,`backend_type`,`backend_table`,`frontend_model`,`frontend_input`,`frontend_label`,`frontend_class`,`source_model`,`is_global`,`is_visible`,`is_required`,`is_user_defined`,`default_value`,`is_searchable`,`is_filterable`,`is_comparable`) values (104,10,'meta_keyword',NULL,NULL,'text',NULL,NULL,'text','Meta Keywords',NULL,NULL,1,1,0,0,NULL,0,0,0),(105,10,'meta_description',NULL,NULL,'text',NULL,NULL,'text','Meta Description',NULL,NULL,1,1,0,0,NULL,0,0,0),(106,10,'image',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(107,10,'shoe_type',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(108,10,'default_category_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(109,10,'small_image',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(110,10,'old_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(111,9,'name',NULL,NULL,'varchar',NULL,NULL,'text','Name',NULL,NULL,0,1,1,0,NULL,0,0,0),(112,9,'description',NULL,NULL,'text',NULL,NULL,'textarea','Description',NULL,NULL,0,1,0,0,NULL,0,0,0),(113,9,'image',NULL,NULL,'varchar',NULL,NULL,'file','Image',NULL,NULL,1,1,0,0,NULL,0,0,0),(114,9,'meta_title',NULL,NULL,'varchar',NULL,NULL,'text','Meta Title',NULL,NULL,1,1,0,0,NULL,0,0,0),(115,9,'meta_keywords',NULL,NULL,'text',NULL,NULL,'textarea','Meta Keywords',NULL,NULL,1,1,0,0,NULL,0,0,0),(116,9,'meta_description',NULL,NULL,'text',NULL,NULL,'textarea','Meta Description',NULL,NULL,1,1,0,0,NULL,0,0,0),(117,9,'landing_page',NULL,NULL,'int',NULL,NULL,'text','Landing Page',NULL,NULL,1,1,0,0,NULL,0,0,0),(118,9,'display_mode',NULL,NULL,'varchar',NULL,NULL,'text','Display Mode',NULL,NULL,1,1,0,0,NULL,0,0,0),(194,4,'grand_total',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(195,4,'currency_rate',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(196,4,'weight',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(197,4,'tax_percent',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(198,4,'subtotal',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(199,4,'discount_amount',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(200,4,'tax_amount',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(201,4,'shipping_amount',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(202,4,'giftcert_amount',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(203,4,'custbalance_amount',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(204,4,'quote_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(205,4,'customer_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0);
insert into `eav_attribute` (`attribute_id`,`entity_type_id`,`attribute_name`,`attribute_model`,`backend_model`,`backend_type`,`backend_table`,`frontend_model`,`frontend_input`,`frontend_label`,`frontend_class`,`source_model`,`is_global`,`is_visible`,`is_required`,`is_user_defined`,`default_value`,`is_searchable`,`is_filterable`,`is_comparable`) values (206,4,'store_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(207,4,'currency_base_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(208,4,'shipping_description',NULL,NULL,'text',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(209,4,'real_order_id',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(210,4,'remote_ip',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(211,4,'currency_code',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(212,4,'coupon_code',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(213,4,'giftcert_code',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(214,4,'shipping_method',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(215,4,'status',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(216,4,'shipping_address_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,0,NULL,0,0,0),(217,4,'billing_address_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,0,NULL,0,0,0),(218,6,'region_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(219,6,'country_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,0,NULL,0,0,0),(220,6,'address_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(221,6,'customer_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(222,6,'street',NULL,NULL,'text',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(223,6,'email',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(224,6,'firstname',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(225,6,'lastname',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(226,6,'company',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(227,6,'city',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(228,6,'region',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(229,6,'postcode',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(230,6,'telephone',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(231,6,'fax',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(232,6,'tax_id',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(233,6,'address_type',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,0,NULL,0,0,0),(234,7,'weight',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(235,7,'qty',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(236,7,'qty_backordered',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(237,7,'qty_canceled',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(238,7,'qty_shipped',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0);
insert into `eav_attribute` (`attribute_id`,`entity_type_id`,`attribute_name`,`attribute_model`,`backend_model`,`backend_type`,`backend_table`,`frontend_model`,`frontend_input`,`frontend_label`,`frontend_class`,`source_model`,`is_global`,`is_visible`,`is_required`,`is_user_defined`,`default_value`,`is_searchable`,`is_filterable`,`is_comparable`) values (239,7,'qty_returned',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(240,7,'price',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(241,7,'tier_price',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(242,7,'cost',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(243,7,'discount_percent',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(244,7,'discount_amount',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(245,7,'tax_percent',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(246,7,'tax_amount',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(247,7,'row_total',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(248,7,'row_weight',NULL,NULL,'decimal',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(249,7,'product_id',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(250,7,'image',NULL,NULL,'text',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(251,7,'name',NULL,NULL,'text',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(252,7,'model',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(253,8,'cc_exp_month',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(254,8,'cc_exp_year',NULL,NULL,'int',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(255,8,'cc_raw_request',NULL,NULL,'text',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(256,8,'cc_raw_response',NULL,NULL,'text',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(257,8,'method',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(258,8,'po_number',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(259,8,'cc_type',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(260,8,'cc_number_enc',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(261,8,'cc_last4',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(262,8,'cc_owner',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(263,8,'cc_trans_id',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(264,8,'cc_approval',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(265,8,'cc_avs_status',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(266,8,'cc_cid_status',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(267,5,'status',NULL,NULL,'varchar',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,0,NULL,0,0,0),(268,5,'comments',NULL,NULL,'text',NULL,NULL,NULL,NULL,NULL,NULL,1,1,0,0,NULL,0,0,0),(269,10,'qty',NULL,NULL,'int',NULL,NULL,'text','Qty',NULL,NULL,1,1,0,0,NULL,0,0,0),(270,10,'tier_price',NULL,NULL,'int',NULL,NULL,NULL,'Tier Price',NULL,NULL,1,1,0,0,NULL,0,0,0);

/*Table structure for table `eav_attribute_group` */

DROP TABLE IF EXISTS `eav_attribute_group`;

CREATE TABLE `eav_attribute_group` (
  `attribute_group_id` smallint(5) unsigned NOT NULL auto_increment,
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_group_name` varchar(255) NOT NULL default '',
  `sort_order` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`attribute_group_id`),
  UNIQUE KEY `attribute_set_id` (`attribute_set_id`,`attribute_group_name`),
  KEY `attribute_set_id_2` (`attribute_set_id`,`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `eav_attribute_group` */

insert into `eav_attribute_group` (`attribute_group_id`,`attribute_set_id`,`attribute_group_name`,`sort_order`) values (1,1,'General',1),(2,2,'General',1),(3,3,'General',1),(4,9,'General',1),(5,10,'General',1),(6,11,'General',1),(7,12,'General',1);

/*Table structure for table `eav_attribute_set` */

DROP TABLE IF EXISTS `eav_attribute_set`;

CREATE TABLE `eav_attribute_set` (
  `attribute_set_id` smallint(5) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_set_name` varchar(255) character set utf8 collate utf8_swedish_ci NOT NULL default '',
  `sort_order` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`attribute_set_id`),
  UNIQUE KEY `entity_type_id` (`entity_type_id`,`attribute_set_name`),
  KEY `entity_type_id_2` (`entity_type_id`,`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

/*Data for the table `eav_attribute_set` */

insert into `eav_attribute_set` (`attribute_set_id`,`entity_type_id`,`attribute_set_name`,`sort_order`) values (1,1,'Default',1),(2,2,'Default',1),(3,3,'Default',1),(9,10,'Default',1),(10,10,'Custom',2),(11,10,'My Set',3),(12,9,'Default',1),(18,4,'Default',1),(19,5,'Default',1),(20,6,'Default',1),(21,7,'Default',1),(22,8,'Default',1);

/*Table structure for table `eav_entity` */

DROP TABLE IF EXISTS `eav_entity`;

CREATE TABLE `eav_entity` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_ENTITY_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ENTITY_STORE` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Entityies';

/*Data for the table `eav_entity` */

insert into `eav_entity` (`entity_id`,`entity_type_id`,`store_id`,`created_at`,`updated_at`,`is_active`) values (1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',1);

/*Table structure for table `eav_entity_attribute` */

DROP TABLE IF EXISTS `eav_entity_attribute`;

CREATE TABLE `eav_entity_attribute` (
  `entity_attribute_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_group_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`entity_attribute_id`),
  UNIQUE KEY `attribute_set_id_2` (`attribute_set_id`,`attribute_id`),
  UNIQUE KEY `attribute_group_id` (`attribute_group_id`,`attribute_id`),
  KEY `attribute_set_id_3` (`attribute_set_id`,`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=271 DEFAULT CHARSET=latin1;

/*Data for the table `eav_entity_attribute` */

insert into `eav_entity_attribute` (`entity_attribute_id`,`entity_type_id`,`attribute_set_id`,`attribute_group_id`,`attribute_id`,`sort_order`) values (1,1,1,1,1,1),(2,1,1,1,2,2),(3,1,1,1,3,3),(4,1,1,1,4,4),(5,1,1,1,5,5),(6,1,1,1,6,6),(7,1,1,1,7,7),(8,1,1,1,8,8),(9,2,2,2,9,1),(10,2,2,2,10,2),(11,2,2,2,11,4),(12,2,2,2,12,5),(13,2,2,2,13,6),(14,2,2,2,14,7),(15,2,2,2,15,8),(16,2,2,2,16,9),(17,2,2,2,17,10),(18,2,2,2,18,11),(19,3,3,3,19,1),(95,2,2,2,95,3),(96,9,12,7,111,1),(97,9,12,7,112,2),(98,10,9,4,96,1),(99,10,9,4,97,2),(100,10,9,4,98,3),(101,10,9,4,99,4),(102,10,9,4,100,5),(103,10,9,4,101,6),(104,10,9,4,102,7),(105,10,9,4,103,8),(106,10,9,4,104,9),(107,10,9,4,105,10),(108,10,9,4,106,11),(109,10,9,4,107,12),(110,10,9,4,108,13),(111,10,9,4,109,14),(112,10,9,4,110,15),(113,9,12,7,113,3),(114,9,12,7,114,4),(115,9,12,7,115,5),(116,9,12,7,116,6),(117,9,12,7,117,7),(118,9,12,7,118,8),(194,8,1,1,256,0),(195,8,1,1,255,0),(196,8,1,1,254,0),(197,8,1,1,253,0),(198,7,1,1,252,0),(199,7,1,1,251,0),(200,7,1,1,250,0),(201,7,1,1,249,0),(202,7,1,1,248,0),(203,7,1,1,247,0),(204,7,1,1,246,0),(205,7,1,1,245,0),(206,7,1,1,244,0),(207,7,1,1,243,0),(208,7,1,1,242,0),(209,7,1,1,241,0),(210,7,1,1,240,0),(211,7,1,1,239,0),(212,7,1,1,238,0),(213,7,1,1,237,0),(214,7,1,1,236,0),(215,7,1,1,235,0),(216,7,1,1,234,0),(217,6,1,1,233,0),(218,6,1,1,232,0),(219,6,1,1,231,0),(220,6,1,1,230,0),(221,6,1,1,229,0),(222,6,1,1,228,0),(223,6,1,1,227,0),(224,6,1,1,226,0),(225,6,1,1,225,0),(226,6,1,1,223,0),(227,6,1,1,224,0),(228,6,1,1,222,0),(229,6,1,1,221,0),(230,6,1,1,220,0);
insert into `eav_entity_attribute` (`entity_attribute_id`,`entity_type_id`,`attribute_set_id`,`attribute_group_id`,`attribute_id`,`sort_order`) values (231,6,1,1,219,0),(232,6,1,1,218,0),(233,4,1,1,217,0),(234,4,1,1,216,0),(235,4,1,1,215,0),(236,4,1,1,214,0),(237,4,1,1,213,0),(238,4,1,1,212,0),(239,4,1,1,211,0),(240,4,1,1,210,0),(241,4,1,1,209,0),(242,4,1,1,208,0),(243,4,1,1,207,0),(244,4,1,1,206,0),(245,4,1,1,205,0),(246,4,1,1,204,0),(247,4,1,1,203,0),(248,4,1,1,202,0),(249,4,1,1,201,0),(250,4,1,1,200,0),(251,4,1,1,199,0),(252,4,1,1,198,0),(253,4,1,1,197,0),(254,4,1,1,196,0),(255,4,1,1,195,0),(256,4,1,1,194,0),(257,8,1,1,257,0),(258,8,1,1,258,0),(259,8,1,1,259,0),(260,8,1,1,260,0),(261,8,1,1,261,0),(262,8,1,1,262,0),(263,8,1,1,263,0),(264,8,1,1,264,0),(265,8,1,1,265,0),(266,8,1,1,266,0),(267,5,1,1,267,0),(268,5,1,1,268,0),(269,10,9,4,269,16),(270,10,9,4,270,17);

/*Table structure for table `eav_entity_decimal` */

DROP TABLE IF EXISTS `eav_entity_decimal`;

CREATE TABLE `eav_entity_decimal` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY` (`entity_id`),
  CONSTRAINT `FK_EAV_ENTITY_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `eav_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EAV_ENTITY_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EAV_ENTITY_DECIMAL_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Decimal values of attributes';

/*Data for the table `eav_entity_decimal` */

/*Table structure for table `eav_entity_int` */

DROP TABLE IF EXISTS `eav_entity_int`;

CREATE TABLE `eav_entity_int` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_INT_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_EAV_ENTITY_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `eav_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EAV_ENTITY_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EAV_ENTITY_INT_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Integer values of attributes';

/*Data for the table `eav_entity_int` */

insert into `eav_entity_int` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (4,1,5,1,1,5);

/*Table structure for table `eav_entity_text` */

DROP TABLE IF EXISTS `eav_entity_text`;

CREATE TABLE `eav_entity_text` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_TEXT_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_EAV_ENTITY_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `eav_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EAV_ENTITY_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EAV_ENTITY_TEXT_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Text values of attributes';

/*Data for the table `eav_entity_text` */

/*Table structure for table `eav_entity_type` */

DROP TABLE IF EXISTS `eav_entity_type`;

CREATE TABLE `eav_entity_type` (
  `entity_type_id` smallint(5) unsigned NOT NULL auto_increment,
  `entity_name` varchar(50) NOT NULL default '',
  `entity_table` varchar(255) NOT NULL default '',
  `value_table_prefix` varchar(255) NOT NULL default '',
  `entity_id_field` varchar(255) NOT NULL default '',
  `is_data_sharing` tinyint(4) NOT NULL default '1',
  `default_attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entity_type_id`),
  KEY `entity_name` (`entity_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `eav_entity_type` */

insert into `eav_entity_type` (`entity_type_id`,`entity_name`,`entity_table`,`value_table_prefix`,`entity_id_field`,`is_data_sharing`,`default_attribute_set_id`) values (1,'customer','customer/entity','','',1,1),(2,'customer_address','customer/entity','','',1,2),(3,'customer_payment','customer/entity','','',1,3),(4,'order','sales/order','','',1,0),(5,'order_status','sales/order','','',1,0),(6,'order_address','sales/order','','',1,0),(7,'order_item','sales/order','','',1,0),(8,'order_payment','sales/order','','',1,0),(9,'catalog_category','catalog/category','','',0,12),(10,'catalog_product','catalog/product','','',0,9);

/*Table structure for table `eav_entity_varchar` */

DROP TABLE IF EXISTS `eav_entity_varchar`;

CREATE TABLE `eav_entity_varchar` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY` (`entity_id`),
  CONSTRAINT `FK_EAV_ENTITY_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `eav_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EAV_ENTITY_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EAV_ENTITY_VARCHAR_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Varchar values of attributes';

/*Data for the table `eav_entity_varchar` */

insert into `eav_entity_varchar` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (1,1,1,1,1,'dmitriy'),(2,1,2,1,1,'soroka'),(3,1,3,1,1,'moshe@varien.com');

/*Table structure for table `eav_value_option` */

DROP TABLE IF EXISTS `eav_value_option`;

CREATE TABLE `eav_value_option` (
  `source_option_id` int(10) unsigned NOT NULL auto_increment,
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `value` varchar(255) character set latin1 NOT NULL default '',
  `label` varchar(255) character set latin1 NOT NULL default '',
  `sort_order` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`source_option_id`),
  UNIQUE KEY `attribute_id` (`attribute_id`,`value`),
  KEY `attribute_id_2` (`attribute_id`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 13312 kB';

/*Data for the table `eav_value_option` */

insert into `eav_value_option` (`source_option_id`,`attribute_id`,`value`,`label`,`sort_order`) values (1,5,'1','Regular customer',1),(2,5,'2','Wholesale',2);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
