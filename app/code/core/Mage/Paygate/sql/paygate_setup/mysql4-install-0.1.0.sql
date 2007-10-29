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
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
drop table if exists paygate_authorizenet_debug;
CREATE TABLE `paygate_authorizenet_debug` (
  `debug_id` int(10) unsigned NOT NULL auto_increment,
  `request_body` text,
  `response_body` text,
  `request_serialized` text,
  `result_serialized` text,
  `request_dump` text,
  `result_dump` text,
  PRIMARY KEY  (`debug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'payment','Payment Methods','text','','','','',70,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'payment/authorizenet','Authorize.net','text','','','','',10,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/authorizenet/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/authorizenet/debug','Enable debug log','select','','','','adminhtml/system_config_source_yesno',20,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/authorizenet/email_customer','Email customer','select','','','','adminhtml/system_config_source_yesno',10,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/authorizenet/login','Login','text','','','','',2,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/authorizenet/merchant_email','Merchant\'s email','text','','','','',11,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/authorizenet/order_status','New order status','select','','','','adminhtml/system_config_source_order_status',4,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/authorizenet/sort_order','Sort order','text','','','','',100,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/authorizenet/test','Test mode','select','','','','adminhtml/system_config_source_yesno',5,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/authorizenet/title','Title','text','','','','',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/authorizenet/trans_key','Transaction key','password','','','','',3,1,1,1,'');

replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'payment/ccsave','Saved CC','text','','','','',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/ccsave/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/ccsave/order_status','New order status','select','','','','adminhtml/system_config_source_order_status',2,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/ccsave/sort_order','Sort order','text','','','','',100,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/ccsave/title','Title','text','','','','',1,1,1,1,'');

replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'payment/checkmo','Check / Money order','text','','','','',5,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/checkmo/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/checkmo/order_status','New order status','select','','','','adminhtml/system_config_source_order_status',2,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/checkmo/sort_order','Sort order','text','','','','',100,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/checkmo/title','Title','text','','','','',1,1,1,1,'');

replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'payment/paypal','Paypal','text','','','','',20,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/paypal/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/paypal/order_status','New order status','select','','','','adminhtml/system_config_source_order_status',2,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/paypal/sort_order','Sort order','text','','','','',100,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/paypal/title','Title','text','','','','',1,1,1,1,'');

replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'payment/purchaseorder','Purchase order','text','','','','',7,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/purchaseorder/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/purchaseorder/order_status','New order status','select','','','','adminhtml/system_config_source_order_status',2,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/purchaseorder/sort_order','Sort order','text','','','','',100,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'payment/purchaseorder/title','Title','text','','','','',1,1,1,1,'');


replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/active','1','0',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/cgi_url','https://secure.authorize.net:443/gateway/transact.dll','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/debug','1','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/email_customer','0','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/login','irub782','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/merchant_email','moshe@varien.com','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/model','paygate/authorizenet','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/order_status','1','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/sort_order','4','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/test','1','0',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/title','Credit Card (Authorize.net)','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/authorizenet/trans_key','3Ryyz3x99mZ68Z94','',0);

replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/ccsave/active','1','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/ccsave/model','payment/ccsave','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/ccsave/sort_order','1','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/ccsave/title','Credit Card','',0);

replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/checkmo/active','1','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/checkmo/model','payment/checkmo','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/checkmo/sort_order','2','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/checkmo/title','Check / Money order','',0);

replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/paypal/active','1','0',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/paypal/model','paygate/paypal','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/paypal/order_status','1','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/paypal/sort_order','5','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/paypal/title','Credit Card (Paypal)','',0);

replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/purchaseorder/active','1','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/purchaseorder/model','payment/purchaseorder','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/purchaseorder/sort_order','3','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/purchaseorder/title','Purchase Order','',0);

replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/verisign/active','1','0',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/verisign/model','paygate/payflow_pro','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/verisign/order_status','1','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/verisign/sort_order','6','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'payment/verisign/title','Credit Card (PayflowPro)','',0);
*/