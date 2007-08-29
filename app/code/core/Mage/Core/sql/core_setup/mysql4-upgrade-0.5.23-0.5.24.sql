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
(2,'carriers/freeshipping','Free Shipping','text','','','','',2,1,1,1,''),
(3,'carriers/freeshipping/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,''),
(3,'carriers/freeshipping/sort_order','Sorting order','text','','','','',100,1,1,1,''),
(3,'carriers/freeshipping/title','Title','text','','','','',2,1,1,1,''),
(3,'carriers/freeshipping/name','Method name','text','','','','',3,1,1,1,''),
(3,'carriers/freeshipping/cutoff_cost','Cutoff cost','text','','','','',4,1,1,1,''),
(2,'carriers/flatrate','Flat Rate','text','','','','',2,1,1,1,''),
(3,'carriers/flatrate/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,''),
(3,'carriers/flatrate/sort_order','Sorting order','text','','','','',100,1,1,1,''),
(3,'carriers/flatrate/title','Title','text','','','','',2,1,1,1,''),
(3,'carriers/flatrate/name','Method name','text','','','','',3,1,1,1,''),
(3,'carriers/flatrate/type','Type','select','','','','adminhtml/system_config_source_shipping_flatrate',4,1,1,1,''),
(3,'carriers/flatrate/price','Price','text','','','','',5,1,1,1,''),
(3,'carriers/tablerate/name','Method name','text','','','','',3,1,1,1,'');
  
replace  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
('default',0,'carriers/freeshipping/active','1','0',0),
('default',0,'carriers/flatrate/active','1','0',0),
('default',0,'carriers/freeshipping/name','Free','0',0),
('default',0,'carriers/flatrate/name','Fixed','0',0),
('default',0,'carriers/tablerate/name','Best Way','0',0),
('default',0,'carriers/freeshipping/title','Free Shipping','0',0),
('default',0,'carriers/flatrate/title','Flat Rate','0',0),
('default',0,'carriers/freeshipping/cutoff_cost','50','0',0),
('default',0,'carriers/flatrate/type','I','0',0),
('default',0,'carriers/flatrate/price','5.00','0',0);

