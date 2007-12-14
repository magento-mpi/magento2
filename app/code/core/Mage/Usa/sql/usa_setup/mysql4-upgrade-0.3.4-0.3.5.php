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

 $this->addConfigField('carriers/ups/allowall', 'Ship to applicable countries', array(
	'frontend_type'=>'select',
	'frontend_class'=>'shipping-applicable-country',
	'source_model'=>'adminhtml/system_config_source_shipping_allspecificcountries',	
	'sort_order'=>'90',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');	
 $this->addConfigField('carriers/ups/specificcountry', 'Ship to Specific countries', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'adminhtml/system_config_source_country',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');	
 $this->addConfigField('carriers/ups/showmethod', 'Show method if not applicable', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');	
 $this->addConfigField('carriers/ups/specificerrmsg', 'Displayed Error Message', array(
	'frontend_type'=>'textarea',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');			

 $this->addConfigField('carriers/usps/allowall', 'Ship to applicable countries', array(
	'frontend_type'=>'select',
	'frontend_class'=>'shipping-applicable-country',	
	'source_model'=>'adminhtml/system_config_source_shipping_allspecificcountries',	
	'sort_order'=>'90',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');	
 $this->addConfigField('carriers/usps/specificcountry', 'Ship to Specific countries', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'adminhtml/system_config_source_country',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');	
 $this->addConfigField('carriers/usps/showmethod', 'Show method if not applicable', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');	
 $this->addConfigField('carriers/usps/specificerrmsg', 'Displayed Error Message', array(
	'frontend_type'=>'textarea',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');			

	
 $this->addConfigField('carriers/fedex/allowall', 'Ship to applicable countries', array(
	'frontend_type'=>'select',
	'frontend_class'=>'shipping-applicable-country',	
	'source_model'=>'adminhtml/system_config_source_shipping_allspecificcountries',	
	'sort_order'=>'90',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');		
 $this->addConfigField('carriers/fedex/specificcountry', 'Ship to Specific countries', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'adminhtml/system_config_source_country',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');	
 $this->addConfigField('carriers/fedex/showmethod', 'Show method if not applicable', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');	
 $this->addConfigField('carriers/fedex/specificerrmsg', 'Displayed Error Message', array(
	'frontend_type'=>'textarea',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');	

 $this->addConfigField('carriers/dhl/allowall', 'Ship to applicable countries', array(
	'frontend_type'=>'select',
	'frontend_class'=>'shipping-applicable-country',	
	'source_model'=>'adminhtml/system_config_source_shipping_allspecificcountries',	
	'sort_order'=>'90',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');	
 $this->addConfigField('carriers/dhl/specificcountry', 'Ship to Specific countries', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'adminhtml/system_config_source_country',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');	
 $this->addConfigField('carriers/dhl/showmethod', 'Show method if not applicable', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');	
 $this->addConfigField('carriers/dhl/specificerrmsg', 'Displayed Error Message', array(
	'frontend_type'=>'textarea',
	'sort_order'=>'91',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');