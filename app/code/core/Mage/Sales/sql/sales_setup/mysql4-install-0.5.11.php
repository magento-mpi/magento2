<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$this->startSetup();

$this->createEntityTables('sales_quote_entity');
$this->createEntityTables('sales_quote_temp');
$this->createEntityTables('sales_order_entity');
$this->createEntityTables('sales_invoice_entity');

$this->run(<<<EOT

DROP TABLE IF EXISTS `sales_order_status`;

CREATE TABLE `sales_order_status` (
  `order_status_id` int(5) unsigned NOT NULL auto_increment,
  `frontend_label` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`order_status_id`),
  UNIQUE KEY `frontend_label` (`frontend_label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_order_status` */

insert  into `sales_order_status`(`order_status_id`,`frontend_label`) values (4,'Cancelled'),(3,'Complete'),(1,'Pending'),(2,'Processing');

EOT
);

$this->run(<<<EOT

insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'sales','Sales','text','','','','',107,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'sales/new_order','New order options','text','','','','',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'sales/new_order/email_identity','Confirmation Email Sender','select','','','','adminhtml/system_config_source_email_identity',1,1,1,1,'');
insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'sales/new_order/email_template','Confirmation Template','select','','','','adminhtml/system_config_source_email_template',2,1,1,1,'');

insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'sales/new_order/email_identity','sales','',0);
insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'sales/new_order/email_template','2','',0);
EOT
);

$this->endSetup();

$this->installEntities($this->getDefaultEntities());