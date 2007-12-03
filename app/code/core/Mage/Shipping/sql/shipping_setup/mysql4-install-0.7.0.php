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

$this->addConfigField('shipping', 'Shipping', array(
	'frontend_type'=>'text',
	'sort_order'=>'50',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('shipping/option', 'Options', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('shipping/option/checkout_multiple', 'Allow Shipping to multiple addresses', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('shipping/origin', 'Origin', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('shipping/origin/country_id', 'Country', array(
	'frontend_type'=>'select',
	'frontend_class'=>'countries',
	'source_model'=>'adminhtml/system_config_source_country',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '223');
$this->addConfigField('shipping/origin/postcode', 'ZIP/Postal Code', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '90034');
$this->addConfigField('shipping/origin/region_id', 'Region/State', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '12');

$this->addConfigField('carriers', 'Shipping Methods', array(
	'frontend_type'=>'text',
	'sort_order'=>'51',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');

$this->addConfigField('carriers/flatrate', 'Flat Rate', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/flatrate/active', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('carriers/flatrate/name', 'Method name', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Fixed');
$this->addConfigField('carriers/flatrate/price', 'Price', array(
	'frontend_type'=>'text',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '5.00');
$this->addConfigField('carriers/flatrate/sort_order', 'Sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/flatrate/title', 'Title', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Flat Rate');
$this->addConfigField('carriers/flatrate/type', 'Type', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_shipping_flatrate',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'I');
$this->addConfigField('carriers/freeshipping', 'Free Shipping', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/freeshipping/active', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('carriers/freeshipping/cutoff_cost', 'Minimum order amount', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '50');
$this->addConfigField('carriers/freeshipping/name', 'Method name', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Free');
$this->addConfigField('carriers/freeshipping/sort_order', 'Sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/freeshipping/title', 'Title', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Free Shipping');
$this->addConfigField('carriers/pickup', 'Pick Up', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'0',
	'show_in_website'=>'0',
	'show_in_store'=>'0',
	), '');
$this->addConfigField('carriers/pickup/active', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('carriers/pickup/sort_order', 'Sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/pickup/title', 'Title', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/tablerate', 'Table rates', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/tablerate/active', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('carriers/tablerate/condition_name', 'Condition', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_shipping_tablerate',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'0',
	), 'package_weight');
$this->addConfigField('carriers/tablerate/export', 'Export', array(
	'frontend_type'=>'export',
	'sort_order'=>'5',
	'show_in_default'=>'0',
	'show_in_website'=>'1',
	'show_in_store'=>'0',
	), '');
$this->addConfigField('carriers/tablerate/import', 'Import', array(
	'frontend_type'=>'import',
	'backend_model'=>'adminhtml/system_config_backend_shipping_tablerate',
	'sort_order'=>'6',
	'show_in_default'=>'0',
	'show_in_website'=>'1',
	'show_in_store'=>'0',
	), '');
$this->addConfigField('carriers/tablerate/name', 'Method name', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Best Way');
$this->addConfigField('carriers/tablerate/sort_order', 'Sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('carriers/tablerate/title', 'Title', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
