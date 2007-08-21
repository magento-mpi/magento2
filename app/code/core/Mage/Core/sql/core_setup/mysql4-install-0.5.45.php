<?php

$this->startSetup();

$this->run(<<<EOT

/*Table structure for table `core_resource` */

DROP TABLE IF EXISTS `core_resource`;

CREATE TABLE `core_resource` (
  `code` varchar(50) NOT NULL default '',
  `version` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Resource version registry';

/*Data for the table `core_resource` */

EOT
);

$this->run(<<<EOT


DROP TABLE IF EXISTS `core_config_data`;

CREATE TABLE `core_config_data` (
  `config_id` int(10) unsigned NOT NULL auto_increment,
  `scope` enum('default','websites','stores','config') NOT NULL default 'default',
  `scope_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default 'general',
  `value` varchar(255) NOT NULL default '',
  `old_value` varchar(255) NOT NULL default '',
  `inherit` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_scope` (`scope`,`scope_id`,`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `core_config_field` */

DROP TABLE IF EXISTS `core_config_field`;

CREATE TABLE `core_config_field` (
  `field_id` int(10) unsigned NOT NULL auto_increment,
  `level` tinyint(1) unsigned NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `frontend_label` varchar(255) NOT NULL default '',
  `frontend_type` varchar(64) NOT NULL default 'text',
  `frontend_class` varchar(255) NOT NULL default '',
  `frontend_model` varchar(255) NOT NULL default '',
  `backend_model` varchar(255) NOT NULL default '',
  `source_model` varchar(255) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  `show_in_default` tinyint(4) NOT NULL default '1',
  `show_in_website` tinyint(4) NOT NULL default '1',
  `show_in_store` tinyint(4) NOT NULL default '1',
  `module_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`field_id`),
  UNIQUE KEY `IDX_PATH` (`path`),
  KEY `path` (`level`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*Table structure for table `core_email_template` */

DROP TABLE IF EXISTS `core_email_template`;

CREATE TABLE `core_email_template` (
  `template_id` int(7) unsigned NOT NULL auto_increment,
  `template_code` varchar(150) default NULL,
  `template_text` text,
  `template_type` int(3) unsigned default NULL,
  `template_subject` varchar(200) default NULL,
  `template_sender_name` varchar(200) default NULL,
  `template_sender_email` varchar(200) character set latin1 collate latin1_general_ci default NULL,
  `added_at` datetime default NULL,
  `modified_at` datetime default NULL,
  PRIMARY KEY  (`template_id`),
  UNIQUE KEY `template_code` (`template_code`),
  KEY `added_at` (`added_at`),
  KEY `modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Email templates';

/*Data for the table `core_email_template` */

insert  into `core_email_template`(`template_id`,`template_code`,`template_text`,`template_type`,`template_subject`,`template_sender_name`,`template_sender_email`,`added_at`,`modified_at`) values (1,'New account (HTML)','Welcome <strong>{{var customer.name}}</strong>!\r\n\r\n<p>Thank you very much for creating an account.</p>\r\n\r\n<p>To officially log in when you\'re visiting our site, simply click on \"Login\" or \"My Account\" located at the top of every page, and then enter your e-mail address and the password you have chosen.</p>\r\n\r\n<p>==========================================<br/>\r\nUse the following values when prompted to log in:<br/>\r\nE-mail: {{var customer.email}}<br/>\r\nPassword: {{var customer.password}}<br/>\r\n==========================================</p>\r\n\r\nWhen you log in to your account, you will be able to do the following:<br/>\r\n\r\n* Proceed through checkout faster when making a purchase<br/>\r\n* Check the status of orders<br/>\r\n* View past orders<br/>\r\n* Make changes to your account information<br/>\r\n* Change your password<br/>\r\n* Store up to 5 alternative shipping addresses (for shipping to multiple family members and friends!)<br/>\r\n\r\nIf you have any questions about your account or any other matter, please feel free to contact us at \r\n<a href=\"mailto:magento@varien.com\">magento@varien.com</a> or by phone at 1-111-111-1111.<br/>\r\n<br/>\r\nThanks again!\r\n',2,'Welcome, {{var customer.name}}!',NULL,NULL,'2007-08-13 12:28:48','2007-08-14 00:31:28'),(2,'New order (HTML)','<strong>Dear {{var billing.name}}</strong>\r\n\r\nThanks for your order!\r\n<div class=\"content\">\r\n    <h1 class=\"page-heading\">Order #{{var order.increment_id}} ({{var order.status}})</h1>\r\n    <table cellspacing=\"0\" width=\"100%\">\r\n        <thead>\r\n            <tr>\r\n                <th style=\"width:50%;\"><h3>Billing Information</h3></th>\r\n                <th style=\"width:50%;\"><h3>Payment Method</h3></th></tr>\r\n        </thead>\r\n        <tbody>\r\n            <tr>\r\n                <td>\r\n                    <address>\r\n                        {{var order.billing_address.getFormated(\'html\')}}\r\n                    </address>\r\n                </td>\r\n                <td class=\"align-center\">\r\n                    {{var order.payment.getFormated(\'html\')}}\r\n                </td>\r\n            </tr>\r\n        </tbody>\r\n    </table>\r\n    <p></p>\r\n    <table cellspacing=\"0\" width=\"100%\">\r\n        <thead>\r\n            <tr>\r\n                <th style=\"width:50%;\"><h3>Shipping Information</h3></th>\r\n                <th style=\"width:50%;\"><h3>Shipping Method</h3></th></tr>\r\n        </thead>\r\n        <tbody>\r\n            <tr>\r\n                <td>\r\n                    <address>\r\n                        {{var order.shipping_address.getFormated(\'html\')}}\r\n                    </address>\r\n                </td>\r\n                <td class=\"align-center\">\r\n                    {{var order.shipping_description}}\r\n                </td>\r\n            </tr>\r\n        </tbody>\r\n    </table>\r\n    <p></p>\r\n    {{include template=\"email/order/items.phtml\"}}\r\n</div>',2,'New Order # {{var order.increment_id}}',NULL,NULL,'2007-08-13 12:29:52','2007-08-15 23:40:20'),(3,'New password (HTML)','<p>Dear {{var customer.name}},</p>\r\n\r\n<p>Your new password is: {{var customer.password}}</p>\r\n\r\n\r\n<p>You can change your password at any time by logging into the \"My Account\" section.</p>\r\n\r\n<p>Thank you very much.</p>\r\n\r\n<p>Your internet buddies and best friends forever,</p>\r\n\r\n<p>Magento Products Office</p>\r\n',2,'New password for {{var customer.name}}',NULL,NULL,'2007-08-13 12:30:10','2007-08-15 23:19:12'),(4,'Order update','Hello, {{var billing.firstname}}',2,'Order # {{var order.increment_id}} update',NULL,NULL,'2007-08-13 16:27:58','2007-08-13 16:28:05'),(5,'New account (Plain)','Welcome {{var customer.name}}!\r\n\r\nThank you very much for creating an account.\r\n\r\nTo officially log in when you\'re visiting our site, simply click on \"Login\" or \"My Account\" located at the top of every page, and then enter your e-mail address and the password you have chosen.\r\n\r\n==========================================\r\n\r\nUse the following values when prompted to log in:\r\n\r\nE-mail: {{var customer.email}}\r\n\r\nPassword: {{var customer.password}}\r\n\r\n==========================================\r\n\r\nWhen you log in to your account, you will be able to do the following:\r\n\r\n* Proceed through checkout faster when making a purchase\r\n\r\n* Check the status of orders\r\n\r\n* View past orders\r\n\r\n* Make changes to your account information\r\n\r\n* Change your password\r\n\r\n* Store up to 5 alternative shipping addresses (for shipping to multiple family members and friends!)\r\n\r\nIf you have any questions about your account or any other matter, please feel free to contact us at \r\nmagento@varien.com or by phone at 1-111-111-1111.\r\n\r\n\r\nThanks again!',2,'Welcome {{var customer.name}}',NULL,NULL,'2007-08-14 00:32:34','2007-08-14 00:32:34'),(6,'Newsletter subscription confirmation (HTML)','Hello,\r\n\r\nThank you for subscribing to our newsletter.\r\n\r\nTo begin receiving the newsletter, you must first confirm your subscription by clicking on the following link:\r\n\r\n<a href=\"{{var newsletter_link}}\">{{var newsletter_link}}</a>\r\n\r\nThanks again!\r\n\r\nSincerely,\r\n\r\nMagento Store.',2,'Newsletter subscription confirmation',NULL,NULL,'2007-08-16 18:31:57','2007-08-16 18:31:57');

/*Table structure for table `core_language` */

DROP TABLE IF EXISTS `core_language`;

CREATE TABLE `core_language` (
  `language_code` varchar(2) NOT NULL default '',
  `language_title` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Languages';

/*Data for the table `core_language` */

insert  into `core_language`(`language_code`,`language_title`) values ('en','English'),('ru','Russian');

/*Table structure for table `core_session` */

DROP TABLE IF EXISTS `core_session`;

CREATE TABLE `core_session` (
  `session_id` varchar(255) NOT NULL default '',
  `website_id` smallint(5) unsigned default NULL,
  `session_expires` int(10) unsigned NOT NULL default '0',
  `session_data` text NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `FK_SESSION_WEBSITE` (`website_id`),
  CONSTRAINT `FK_SESSION_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Session data store';

/*Data for the table `core_session` */

/*Table structure for table `core_store` */

DROP TABLE IF EXISTS `core_store`;

CREATE TABLE `core_store` (
  `store_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `language_code` varchar(2) default NULL,
  `website_id` smallint(5) unsigned default '0',
  `name` varchar(32) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  `is_active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`store_id`),
  UNIQUE KEY `code` (`code`),
  KEY `FK_STORE_LANGUAGE` (`language_code`),
  KEY `FK_STORE_WEBSITE` (`website_id`),
  KEY `is_active` (`is_active`,`sort_order`),
  CONSTRAINT `FK_STORE_LANGUAGE` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_STORE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores';

/*Data for the table `core_store` */

insert  into `core_store`(`store_id`,`code`,`language_code`,`website_id`,`name`,`sort_order`,`is_active`) values (0,'default','en',0,'Default',0,1),(1,'base','en',1,'English Store',0,1),(2,'russian','ru',1,'Russian Store',0,1),(3,'gifts','en',2,'Gifts store',0,1),(4,'vipgifts','en',2,'VIP Gifts',0,1);

/*Table structure for table `core_translate` */

DROP TABLE IF EXISTS `core_translate`;

CREATE TABLE `core_translate` (
  `key_id` int(10) unsigned NOT NULL auto_increment,
  `string` varchar(255) NOT NULL default '',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `translate` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`key_id`),
  UNIQUE KEY `IDX_CODE` (`string`,`store_id`),
  KEY `FK_CORE_TRANSLATE_STORE` (`store_id`),
  CONSTRAINT `FK_CORE_TRANSLATE_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Translation data';

/*Data for the table `core_translate` */

/*Table structure for table `core_website` */

DROP TABLE IF EXISTS `core_website`;

CREATE TABLE `core_website` (
  `website_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  `is_active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`website_id`),
  UNIQUE KEY `code` (`code`),
  KEY `is_active` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites';

/*Data for the table `core_website` */

insert  into `core_website`(`website_id`,`code`,`name`,`sort_order`,`is_active`) values (0,'default','Default',0,1),(1,'base','Main Website',0,1),(2,'gift','Gifts Website',0,1);


EOT
);

$this->run(<<<EOT

insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'advanced','Advanced','text','','','','',100,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'advanced/datashare','Datasharing','text','','','','',1,0,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'advanced/datashare/default','Default','multiselect','','','adminhtml/system_config_backend_datashare','adminhtml/system_config_source_store',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'advanced/modules_disable_output','Disable modules output','text','','adminhtml/system_config_form_fieldset_modules_disableOutput','','',3,1,1,1,'');


insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'design','Design','text','','','','',25,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'design/package','Package','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'design/package/name','Current package name','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'design/theme','Themes','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'design/theme/default','Default','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'design/theme/layout','Layout','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'design/theme/skin','Skin (Images / CSS)','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'design/theme/template','Templates','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'design/theme/translate','Translations','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'dev','Developer','text','','','','',103,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'dev/debug','Debug','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'dev/debug/profiler','Profiler','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'dev/mode','Operating mode','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'dev/mode/checksum','Validate config checksums','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'general','General','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'general/country','Countries options','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/country/allow','Allow countries','multiselect','','','','adminhtml/system_config_source_country',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/country/default','Default country','select','','','','adminhtml/system_config_source_country',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'general/currency','Currency options','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/currency/allow','Allow currencies','multiselect','','','','adminhtml/system_config_source_currency',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/currency/base','Base currency','select','','','','adminhtml/system_config_source_currency',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/currency/default','Default currency','select','','','','adminhtml/system_config_source_currency',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'general/local','Local options','text','','','','',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/local/date_format_long','Date format (long)','text','','','','',4,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/local/date_format_medium','Date format (medium)','text','','','','',5,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/local/date_format_short','Date format (short)','select','','','','adminhtml/system_config_source_date_short',6,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/local/datetime_format_long','Date format (long with time)','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/local/datetime_format_medium','Date format (medium with time)','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/local/datetime_format_short','Date format (short with time)','text','','','','',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'general/local/language','Language','select','','','','adminhtml/system_config_source_language',7,1,1,1,'');


insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'system','System','text','','','','',80,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'system/filesystem','Filesystem','text','','','','',1,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/base','Base directory','text','','','','',1,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/cache_config','Config cache directory','text','','','','',2,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/cache_layout','Layout cache directory','text','','','','',3,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/code','Code pools root directory','text','','','','',4,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/etc','Configuration directory','text','','','','',5,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/layout','Layout files directory','text','','','','',6,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/media','Media files directory','text','','','','',7,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/session','Session files directory','text','','','','',8,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/skin','Skin directory','text','','','','',9,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/template','Template directory','text','','','','',10,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/translate','Translactions directory','text','','','','',11,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/upload','Upload directory','text','','','','',12,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'system/filesystem/var','Var (temporary files) directory','text','','','','',13,1,0,0,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'trans_email','Transactional emails','text','','','','',101,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'trans_email/ident_custom1','Custom email 1','text','','','','',4,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'trans_email/ident_custom1/email','Sender email','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'trans_email/ident_custom1/name','Sender name','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'trans_email/ident_custom2','Custom email 2','text','','','','',5,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'trans_email/ident_custom2/email','Sender email','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'trans_email/ident_custom2/name','Sender name','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'trans_email/ident_general','General contact','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'trans_email/ident_general/email','Sender email','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'trans_email/ident_general/name','Sender name','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'trans_email/ident_sales','Sales representative','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'trans_email/ident_sales/email','Sender email','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'trans_email/ident_sales/name','Sender name','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'trans_email/ident_support','Customer support','text','','','','',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'trans_email/ident_support/email','Sender email','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'trans_email/ident_support/name','Sender name','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'web','Web','text','','','','',20,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'web/cookie','Cookie management','text','','','','',4,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/cookie/cookie_domain','Cookie Domain','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/cookie/cookie_lifetime','Cookie Lifetime','text','','','','',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/cookie/cookie_path','Cookie Path','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'web/default','Default URLs','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/default/front','Default web url','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/default/no_route','Default no-route url','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'web/secure','Secure','text','','','','',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/secure/base_path','Base url','text','','','','',4,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/secure/host','Host','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/secure/port','Port','text','','','','',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/secure/protocol','Protocol','select','','','','adminhtml/system_config_source_web_protocol',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'web/unsecure','Unsecure','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/unsecure/base_path','Base url','text','','','','',4,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/unsecure/host','Host','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/unsecure/port','Port','text','','','','',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/unsecure/protocol','Protocol','select','','','','adminhtml/system_config_source_web_protocol',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'web/url','URLs','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/url/js','Js base url','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/url/media','Media base url','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/url/skin','Skin base url','text','','','','',3,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web/url/upload','Upload files URL','text','','','','',4,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'web_track','Web tracking','text','','','','',106,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'web_track/google','Google analytics','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web_track/google/urchin_account','Account number','text','','','','',2,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'web_track/google/urchin_enable','Enable','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');


insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'design/package/area','frontend','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'design/package/layout','default','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'design/package/name','default','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'design/package/skin','default','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'design/package/template','default','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'design/package/theme','default','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'design/package/translate','default','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'dev/debug/profiler','1','0',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'dev/mode/checksum','1','',0);

insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/country/allow','38,220,223','US,CA,UA',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/country/default','223','US',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/currency/allow','CAD,RUB,UAH,USD','USD,CAD,UAH,RUB',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/currency/base','USD','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/currency/default','USD','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/local/date_format_long','%A, %B %e %Y','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/local/date_format_medium','%a, %b %e %Y','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/local/date_format_short','%m/%d/%y','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/local/datetime_format_long','%A, %B %e %Y [%I:%M %p]','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/local/datetime_format_medium','%a, %b %e %Y [%I:%M %p]','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/local/datetime_format_short','%m/%d/%y [%I:%M %p]','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'general/local/language','en','',0);


insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/base','{{root_dir}}','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/cache_config','{{var_dir}}/cache/config','{{var_dir}}/cache/config/',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/cache_layout','{{var_dir}}/cache/layout','{{var_dir}}/cache/layout/',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/code','{{app_dir}}/code','{{app_dir}}/code/',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/design','{{app_dir}}/design','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/etc','{{app_dir}}/etc','{{app_dir}}/etc/',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/layout','{{app_dir}}/design/frontend/default/layout/default','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/media','{{root_dir}}/media','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/session','{{var_dir}}/session','{{var_dir}}/session/',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/skin','{{root_dir}}/skin','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/template','{{app_dir}}/design/frontend/default/template/default','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/translate','{{app_dir}}/design/frontend/default/translate','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/upload','{{root_dir}}/media/upload','{{root_dir}}/media/upload/',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'system/filesystem/var','{{var_dir}}','',0);

insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'trans_email/ident_custom1/email','custom1@magento.varien.com','moshe@varien.com',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'trans_email/ident_custom1/name','Custom 1','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'trans_email/ident_custom2/email','custom2@magento.varien.com','moshe@varien.com',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'trans_email/ident_custom2/name','Custom 2','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'trans_email/ident_general/email','general@magento.varien.com','moshe@varien.com',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'trans_email/ident_general/name','General','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'trans_email/ident_sales/email','sales@magento.varien.com','moshe@varien.com',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'trans_email/ident_sales/name','Sales','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'trans_email/ident_support/email','support@magento.varien.com','moshe@varien.com',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'trans_email/ident_support/name','Customer support','',0);


insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/cookie/cookie_domain','.varien.com','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/cookie/cookie_lifetime','3600','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/cookie/cookie_path','/','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/default/front','catalog','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/default/no_route','cms/index/noRoute','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/secure/base_path','{{base_path}}','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/secure/host','{{host}}','{{host}}',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/secure/port','81','444',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/secure/protocol','http','https',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/unsecure/base_path','{{base_path}}','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/unsecure/host','{{host}}','{{host}}',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/unsecure/port','81','{{port}}',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/unsecure/protocol','http','{{protocol}}',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/url/js','{{base_path}}js/','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/url/media','{{base_path}}media/','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/url/skin','{{base_path}}skin/','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'web/url/upload','{{base_path}}media/upload/','',0);
EOT
);

$this->endSetup();

#Mage::getSingleton('core/store')->updateDatasharing();