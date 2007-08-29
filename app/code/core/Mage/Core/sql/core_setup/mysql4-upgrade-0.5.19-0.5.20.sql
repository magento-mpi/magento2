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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values 
(3,'carriers/usps/userid','User ID','text','','','','',3,1,1,1,''),
(3,'carriers/usps/container','Container','select','','','','usa/shipping_carrier_usps_source_container',4,1,1,1,''),
(3,'carriers/usps/size','Size','select','','','','usa/shipping_carrier_usps_source_size',5,1,1,1,''),
(3,'carriers/usps/machinable','Machinable','select','','','','usa/shipping_carrier_usps_source_machinable',6,1,1,1,''),
(3,'carriers/usps/handling','Handling fee','text','','','','',7,1,1,1,'');
  
replace  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
('default',0,'carriers/usps/userid','652VARIE8323','',0); 
('default',0,'carriers/usps/active','1','0',0),
('default',0,'carriers/usps/gateway_url','http://testing.shippingapis.com/ShippingAPITest.dll','',0),
('default',0,'carriers/usps/title','United States Postal Service','',0),
('stores',1,'carriers/usps/title','U.S.P.S.','',0),
('default',0,'carriers/usps/container','VARIABLE','',0),
('default',0,'carriers/usps/size','REGULAR','',0),
('default',0,'carriers/usps/machinable','false','',0);

