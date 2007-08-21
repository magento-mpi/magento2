<?php

$this->startSetup();

$this->run(<<<EOT

DROP TABLE IF EXISTS `newsletter_problem`;

CREATE TABLE `newsletter_problem` (
  `problem_id` int(7) unsigned NOT NULL auto_increment,
  `subscriber_id` int(7) unsigned default NULL,
  `queue_id` int(7) unsigned NOT NULL default '0',
  `problem_error_code` int(3) unsigned default '0',
  `problem_error_text` varchar(200) default NULL,
  PRIMARY KEY  (`problem_id`),
  KEY `FK_PROBLEM_SUBSCRIBER` (`subscriber_id`),
  KEY `FK_PROBLEM_QUEUE` (`queue_id`),
  CONSTRAINT `FK_PROBLEM_QUEUE` FOREIGN KEY (`queue_id`) REFERENCES `newsletter_queue` (`queue_id`),
  CONSTRAINT `FK_PROBLEM_SUBSCRIBER` FOREIGN KEY (`subscriber_id`) REFERENCES `newsletter_subscriber` (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter problems';

/*Data for the table `newsletter_problem` */

/*Table structure for table `newsletter_queue` */

DROP TABLE IF EXISTS `newsletter_queue`;

CREATE TABLE `newsletter_queue` (
  `queue_id` int(7) unsigned NOT NULL auto_increment,
  `template_id` int(7) unsigned NOT NULL default '0',
  `queue_status` int(3) unsigned NOT NULL default '0',
  `queue_start_at` datetime default NULL,
  `queue_finish_at` datetime default NULL,
  PRIMARY KEY  (`queue_id`),
  KEY `FK_QUEUE_TEMPLATE` (`template_id`),
  CONSTRAINT `FK_QUEUE_TEMPLATE` FOREIGN KEY (`template_id`) REFERENCES `newsletter_template` (`template_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter queue';

/*Data for the table `newsletter_queue` */

/*Table structure for table `newsletter_queue_link` */

DROP TABLE IF EXISTS `newsletter_queue_link`;

CREATE TABLE `newsletter_queue_link` (
  `queue_link_id` int(9) unsigned NOT NULL auto_increment,
  `queue_id` int(7) unsigned NOT NULL default '0',
  `subscriber_id` int(7) unsigned NOT NULL default '0',
  `letter_sent_at` datetime default NULL,
  PRIMARY KEY  (`queue_link_id`),
  KEY `FK_QUEUE_LINK_SUBSCRIBER` (`subscriber_id`),
  KEY `FK_QUEUE_LINK_QUEUE` (`queue_id`),
  CONSTRAINT `FK_QUEUE_LINK_SUBSCRIBER` FOREIGN KEY (`subscriber_id`) REFERENCES `newsletter_subscriber` (`subscriber_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_QUEUE_LINK_QUEUE` FOREIGN KEY (`queue_id`) REFERENCES `newsletter_queue` (`queue_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter queue to subscriber link';

/*Data for the table `newsletter_queue_link` */

/*Table structure for table `newsletter_queue_store_link` */

DROP TABLE IF EXISTS `newsletter_queue_store_link`;

CREATE TABLE `newsletter_queue_store_link` (
  `queue_id` int(7) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`queue_id`,`store_id`),
  CONSTRAINT `FK_LINK_QUEUE` FOREIGN KEY (`queue_id`) REFERENCES `newsletter_queue` (`queue_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `newsletter_queue_store_link` */

/*Table structure for table `newsletter_subscriber` */

DROP TABLE IF EXISTS `newsletter_subscriber`;

CREATE TABLE `newsletter_subscriber` (
  `subscriber_id` int(7) unsigned NOT NULL auto_increment,
  `store_id` int(3) unsigned default '0',
  `change_status_at` datetime default NULL,
  `customer_id` int(11) unsigned NOT NULL default '0',
  `subscriber_email` varchar(150) character set latin1 collate latin1_general_ci NOT NULL default '',
  `subscriber_status` int(3) NOT NULL default '0',
  `subscriber_confirm_code` varchar(32) default 'NULL',
  PRIMARY KEY  (`subscriber_id`),
  KEY `FK_SUBSCRIBER_STORE` (`store_id`),
  KEY `FK_SUBSCRIBER_CUSTOMER` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter subscribers';

/*Data for the table `newsletter_subscriber` */

/*Table structure for table `newsletter_template` */

DROP TABLE IF EXISTS `newsletter_template`;

CREATE TABLE `newsletter_template` (
  `template_id` int(7) unsigned NOT NULL auto_increment,
  `template_code` varchar(150) default NULL,
  `template_text` text,
  `template_text_preprocessed` text,
  `template_type` int(3) unsigned default NULL,
  `template_subject` varchar(200) default NULL,
  `template_sender_name` varchar(200) default NULL,
  `template_sender_email` varchar(200) character set latin1 collate latin1_general_ci default NULL,
  `template_actual` tinyint(1) unsigned default '1',
  `added_at` datetime default NULL,
  `modified_at` datetime default NULL,
  PRIMARY KEY  (`template_id`),
  KEY `template_actual` (`template_actual`),
  KEY `added_at` (`added_at`),
  KEY `modified_at` (`modified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter templates';


EOT
);

$this->run(<<<EOT

insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'newsletter','Newsletter','text','','','','',105,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'newsletter/subscription','Subscription Options','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'newsletter/subscription/confirm','Need Confirmation','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'newsletter/subscription/confirm_email_identity','Confirmation Email Sender','select','','','','adminhtml/system_config_source_email_identity',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'newsletter/subscription/confirm_email_template','Confirmation Email Template','select','','','','adminhtml/system_config_source_email_template',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'newsletter/subscription/success_email_identity','Success Email Sender','select','','','','adminhtml/system_config_source_email_identity',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'newsletter/subscription/success_email_template','Success Email Template','select','','','','adminhtml/system_config_source_email_template',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'newsletter/subscription/un_email_identity','Unsubscription Email Sender','select','','','','adminhtml/system_config_source_email_identity',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'newsletter/subscription/un_email_template','Unsubscription Email Template','select','','','','adminhtml/system_config_source_email_template',1,1,1,1,'');


EOT
);

$this->endSetup();