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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->addConfigField('carriers/dhl', 'DHL', array(
	'frontend_type'=>'text',
	'sort_order'=>'13',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/dhl/account', 'Account number', array(
	'frontend_type'=>'text',
	'sort_order'=>'7',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'MAGENTO');
$this->addConfigField('carriers/dhl/active', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('carriers/dhl/allowed_methods', 'Allowed methods', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'usa/shipping_carrier_dhl_source_method',
	'sort_order'=>'17',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'E,N,S,G');
$this->addConfigField('carriers/dhl/cutoff_cost', 'Minimum order amount for free shipping', array(
	'frontend_type'=>'text',
	'sort_order'=>'21',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/dhl/free_method', 'Free method', array(
	'frontend_type'=>'select',
	'frontend_class'=>'free-method',
	'source_model'=>'usa/shipping_carrier_dhl_source_freemethod',
	'sort_order'=>'20',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'G');
$this->addConfigField('carriers/dhl/gateway_url', 'Gateway URL', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'https://eCommerce.airborne.com/ApiLandingTest.asp');
$this->addConfigField('carriers/dhl/handling', 'Handling fee', array(
	'frontend_type'=>'text',
	'sort_order'=>'10',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/dhl/id', 'Access ID', array(
	'frontend_type'=>'text',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'MAGENTO');
$this->addConfigField('carriers/dhl/password', 'Password', array(
	'frontend_type'=>'text',
	'sort_order'=>'6',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '123123');
$this->addConfigField('carriers/dhl/shipment_type', 'Shipment type', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_dhl_source_shipmenttype',
	'sort_order'=>'9',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'P');
$this->addConfigField('carriers/dhl/shipping_key', 'Shipping key', array(
	'frontend_type'=>'text',
	'sort_order'=>'8',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/dhl/sort_order', 'Sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/dhl/title', 'Title', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'DHL');
$this->addConfigField('carriers/fedex', 'FedEx', array(
	'frontend_type'=>'text',
	'sort_order'=>'12',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/fedex/account', 'Account ID', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/fedex/active', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('carriers/fedex/allowed_methods', 'Allowed methods', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'usa/shipping_carrier_fedex_source_method',
	'sort_order'=>'17',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'PRIORITYOVERNIGHT,STANDARDOVERNIGHT,FIRSTOVERNIGHT,FEDEX2DAY,FEDEXEXPRESSSAVER,INTERNATIONALPRIORITY,INTERNATIONALECONOMY,INTERNATIONALFIRST,FEDEX1DAYFREIGHT,FEDEX2DAYFREIGHT,FEDEX3DAYFREIGHT,FEDEXGROUND,GROUNDHOMEDELIVERY,INTERNATIONALPRIORITY FREIGHT,INTERNATIONALECONOMY FREIGHT,EUROPEFIRSTINTERNATIONALPRIORITY');
$this->addConfigField('carriers/fedex/cutoff_cost', 'Minimum order amount for free shipping', array(
	'frontend_type'=>'text',
	'sort_order'=>'21',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/fedex/dropoff', 'Dropoff', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_fedex_source_dropoff',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'REGULARPICKUP');
$this->addConfigField('carriers/fedex/free_method', 'Free method', array(
	'frontend_type'=>'select',
	'frontend_class'=>'free-method',
	'source_model'=>'usa/shipping_carrier_fedex_source_freemethod',
	'sort_order'=>'20',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'FEDEXGROUND');
$this->addConfigField('carriers/fedex/gateway_url', 'Gateway URL', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'https://gateway.fedex.com/GatewayDC');
$this->addConfigField('carriers/fedex/handling', 'Handling fee', array(
	'frontend_type'=>'text',
	'sort_order'=>'6',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/fedex/packaging', 'Packaging', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_fedex_source_packaging',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'YOURPACKAGING');
$this->addConfigField('carriers/fedex/sort_order', 'Sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/fedex/title', 'Title', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Federal Express');
	
$this->addConfigField('carriers/ups', 'UPS', array(
	'frontend_type'=>'text',
	'sort_order'=>'10',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/ups/access_license_number', 'Access license number', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/ups/active', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('carriers/ups/allowed_methods', 'Allowed methods', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'usa/shipping_carrier_ups_source_method',
	'sort_order'=>'17',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1DM,1DML,1DA,1DAL,1DAPI,1DP,1DPL,2DM,2DML,2DA,2DAL,3DS,GND,GNDCOM,GNDRES,STD,XPR,WXS,XPRL,XDM,XDML,XPD');
$this->addConfigField('carriers/ups/container', 'Container', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_ups_source_container',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'CP');
$this->addConfigField('carriers/ups/cutoff_cost', 'Minimum order amount for free shipping', array(
	'frontend_type'=>'text',
	'sort_order'=>'21',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/ups/dest_type', 'Destination type', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_ups_source_destType',
	'sort_order'=>'6',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'RES');
$this->addConfigField('carriers/ups/free_method', 'Free method', array(
	'frontend_type'=>'select',
	'frontend_class'=>'free-method',
	'source_model'=>'usa/shipping_carrier_ups_source_freemethod',
	'sort_order'=>'20',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'GND');
$this->addConfigField('carriers/ups/gateway_url', 'Gateway URL', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'http://www.ups.com:80/using/services/rave/qcostcgi.cgi');
$this->addConfigField('carriers/ups/gateway_xml_url', 'Gateway XML URL', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'https://www.ups.com/ups.app/xml/Rate');
$this->addConfigField('carriers/ups/handling', 'Handling fee', array(
	'frontend_type'=>'text',
	'sort_order'=>'7',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('carriers/ups/origin_shipment', 'Origin of the shipment', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_ups_source_originShipment',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/ups/password', 'Password', array(
	'frontend_type'=>'password',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/ups/pickup', 'Pickup method', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_ups_source_pickup',
	'sort_order'=>'8',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'CC');
$this->addConfigField('carriers/ups/sort_order', 'Sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/ups/title', 'Title', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'United Parcel Service');
$this->addConfigField('carriers/ups/type', 'UPS type', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_ups_source_type',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/ups/username', 'UserId', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/usps', 'USPS', array(
	'frontend_type'=>'text',
	'sort_order'=>'11',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/usps/active', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('carriers/usps/allowed_methods', 'Allowed methods', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'usa/shipping_carrier_usps_source_method',
	'sort_order'=>'17',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'First-Class,Express Mail,Express Mail PO to PO,Priority Mail,Parcel Post,Express Mail Flat-Rate Envelope,Priority Mail Flat-Rate Box,Bound Printed Matter,Media Mail,Library Mail,Priority Mail Flat-Rate Envelope,Global Express Guaranteed,Global Express Guaranteed Non-Document Rectangular,Global Express Guaranteed Non-Document Non-Rectangular,Express Mail International (EMS),Express Mail International (EMS) Flat Rate Envelope,Priority Mail International,Priority Mail International Flat Rate Box');
$this->addConfigField('carriers/usps/container', 'Container', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_usps_source_container',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'VARIABLE');
$this->addConfigField('carriers/usps/cutoff_cost', 'Minimum order amount for free shipping', array(
	'frontend_type'=>'text',
	'sort_order'=>'21',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/usps/free_method', 'Free method', array(
	'frontend_type'=>'select',
	'frontend_class'=>'free-method',
	'source_model'=>'usa/shipping_carrier_usps_source_freemethod',
	'sort_order'=>'20',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/usps/gateway_url', 'Gateway URL', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/usps/handling', 'Handling fee', array(
	'frontend_type'=>'text',
	'sort_order'=>'7',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/usps/machinable', 'Machinable', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_usps_source_machinable',
	'sort_order'=>'6',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'true');
$this->addConfigField('carriers/usps/size', 'Size', array(
	'frontend_type'=>'select',
	'source_model'=>'usa/shipping_carrier_usps_source_size',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'REGULAR');
$this->addConfigField('carriers/usps/sort_order', 'Sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/usps/title', 'Title', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/usps/userid', 'User ID', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
