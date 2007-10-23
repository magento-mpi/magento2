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
 * @package    Mage_Poll
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->addConfigField('carriers/ups/type', 'UPS type', array(
    'frontend_type'=>'select',
    'source_model'=>'usa/shipping_carrier_ups_source_type',
    'sort_order'=>2
)); 
$this->addConfigField('carriers/ups/gateway_xml_url', 'Gateway XML URL',
	 array(
    	'frontend_type'=>'text',
    	'sort_order'=>3,
    	),
    'https://www.ups.com/ups.app/xml/Rate'
); 
$this->addConfigField('carriers/ups/username', 'UserId', 
	array(
    'frontend_type'=>'text',
    'sort_order'=>3,
)); 
$this->addConfigField('carriers/ups/password', 'Password', 
	array(
    'frontend_type'=>'password',
    'sort_order'=>3,
)); 
$this->addConfigField('carriers/ups/access_license_number', 'Access license number', 
	array(
    'frontend_type'=>'text',
    'sort_order'=>3,
));
$this->addConfigField('carriers/ups/origin_shipment', 'Origin of the shipment', 
	array(
    'frontend_type'=>'select',
    'source_model'=>'usa/shipping_carrier_ups_source_originShipment',
    'sort_order' => 3
)); 

$this->run("
UPDATE core_config_field SET sort_order = '5' WHERE field_id =235 LIMIT 1;
UPDATE core_config_field SET sort_order = '5' WHERE field_id =235 LIMIT 1;
UPDATE core_config_field SET sort_order = '6' WHERE field_id =236 LIMIT 1;
UPDATE core_config_field SET sort_order = '4' WHERE field_id =92 LIMIT 1;
UPDATE core_config_field SET sort_order = '7' WHERE field_id =237 LIMIT 1;
UPDATE core_config_field SET sort_order = '8' WHERE field_id =234 LIMIT 1;
UPDATE core_config_field SET sort_order = '4' WHERE field_id =226 LIMIT 1;
");
?>