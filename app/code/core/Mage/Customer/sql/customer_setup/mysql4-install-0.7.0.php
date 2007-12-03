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

$this->addConfigField('customer', 'Customers', array(
	'frontend_type'=>'text',
	'sort_order'=>'31',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('customer/create_account', 'Create New Account Options', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('customer/create_account/confirm', 'Need to Confirm', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('customer/create_account/confirm', 'Need to Confirm', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('customer/create_account/default_group', 'Default Group', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_customer_group',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('customer/create_account/default_group', 'Default Group', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_customer_group',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('customer/create_account/email_domain', 'Default Email Domain', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'emaildomain.com');
$this->addConfigField('customer/create_account/email_identity', 'Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'general');
$this->addConfigField('customer/create_account/email_identity', 'Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'general');
$this->addConfigField('customer/create_account/email_template', 'Email Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('customer/create_account/email_template', 'Email Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('customer/password', 'Password Options', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('customer/password/forgot_email_identity', 'Forgot Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'support');
$this->addConfigField('customer/password/forgot_email_identity', 'Forgot Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'support');
$this->addConfigField('customer/password/forgot_email_template', 'Forgot Email Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '3');
$this->addConfigField('customer/password/forgot_email_template', 'Forgot Email Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '3');
