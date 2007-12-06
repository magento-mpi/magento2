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
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$this->addConfigField('carriers/ups/tracking_xml_url', 'Tracking XML URL',
	 array(
    	'frontend_type'=>'text',
    	'sort_order'=>6,
    	),
    'https://www.ups.com/ups.app/xml/Track'
); 


$this->addConfigField('carriers/ups/unit_of_measure', 'Weight Unit',
	 array(
    	'frontend_type'=>'select',
    	'source_model'=>'usa/shipping_carrier_ups_source_unitofmeasure',
    	'sort_order'=>6,
    	),
    'LBS'
); 

$this->addConfigField('carriers/dhl/shipping_intlkey', 'Shipping key (International)', array(
	'frontend_type'=>'text',
	'sort_order'=>'8',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
	
$this->addConfigField('carriers/dhl/dutiable', 'Shipment Dutiable', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'11',	
	), '0');
	
$this->addConfigField('carriers/dhl/dutypaymenttype', 'Shipment Duty Payment Type', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_dhl_source_dutypaymenttype',
	'sort_order'=>'12',	
	), 'R');
$this->addConfigField('carriers/dhl/contentdesc', 'Package Description', array(
	'frontend_type'=>'text',	
	'sort_order'=>'12',	
	), 'Big Box');
?>
