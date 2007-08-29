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
(3,'carriers/dhl/id','Access ID','text','','','','',5,1,1,1,''),
(3,'carriers/dhl/password','Password','text','','','','',6,1,1,1,''),
(3,'carriers/dhl/account','Account number','text','','','','',7,1,1,1,''),
(3,'carriers/dhl/shipping_key','Shipping key','text','','','','',8,1,1,1,''),
(3,'carriers/dhl/shipment_type','Shipment type','select','','','','usa/shipping_carrier_dhl_source_shipmenttype',9,1,1,1,''), 
(3,'carriers/dhl/handling','Handling fee','text','','','','',10,1,1,1,'');
  
replace  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
('default',0,'carriers/dhl/id','MAGENTO','',0), 
('default',0,'carriers/dhl/password','123123','',0),
('default',0,'carriers/dhl/account','MAGENTO','',0),
('default',0,'carriers/dhl/shipping_key','','',0),
('default',0,'carriers/dhl/shipment_type','P','',0),
('default',0,'carriers/dhl/active','1','0',0),
('default',0,'carriers/dhl/gateway_url','https://eCommerce.airborne.com/ApiLandingTest.asp','',0),
('default',0,'carriers/dhl/title','DHL','',0);

