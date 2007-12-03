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
$this->addConfigField('payment/authorizenet', 'Authorize.net', array(
	'frontend_type'=>'text',
	'sort_order'=>'10',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('payment/authorizenet/active', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_payment_active',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('payment/authorizenet/email_customer', 'Email customer', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'10',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('payment/authorizenet/login', 'API Login ID', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('payment/authorizenet/merchant_email', 'Merchant\'s email', array(
	'frontend_type'=>'text',
	'sort_order'=>'11',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('payment/authorizenet/order_status', 'New order status', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_order_status',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('payment/authorizenet/sort_order', 'Sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '4');
$this->addConfigField('payment/authorizenet/test', 'Test mode', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('payment/authorizenet/title', 'Title', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Credit Card (Authorize.net)');
$this->addConfigField('payment/authorizenet/trans_key', 'Transaction key', array(
	'frontend_type'=>'password',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('payment/verisign', 'PayflowPro', array(
	'frontend_type'=>'text',
	'sort_order'=>'30',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('payment/verisign/active', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_payment_active',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('payment/verisign/order_status', 'New order status', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_order_status',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('payment/verisign/partner', 'Partner', array(
	'frontend_type'=>'text',
	'sort_order'=>'0',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('payment/verisign/pwd', 'Password', array(
	'frontend_type'=>'text',
	'sort_order'=>'0',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('payment/verisign/sort_order', 'Sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '6');
$this->addConfigField('payment/verisign/tender', 'TENDER', array(
	'frontend_type'=>'text',
	'sort_order'=>'0',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'C');
$this->addConfigField('payment/verisign/title', 'Title', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Credit Card (Verisign)');
$this->addConfigField('payment/verisign/url', 'URL', array(
	'frontend_type'=>'text',
	'sort_order'=>'0',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'https://pilot-payflowpro.verisign.com/transaction');
$this->addConfigField('payment/verisign/user', 'User', array(
	'frontend_type'=>'text',
	'sort_order'=>'0',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('payment/verisign/vendor', 'Vendor', array(
	'frontend_type'=>'text',
	'sort_order'=>'0',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('payment/verisign/verbosity', 'VERBOSITY', array(
	'frontend_type'=>'text',
	'sort_order'=>'0',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'MEDIUM');
