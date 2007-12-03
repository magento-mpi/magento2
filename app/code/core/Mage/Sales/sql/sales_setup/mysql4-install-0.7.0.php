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

$this->addConfigField('sales', 'Sales', array(
	'frontend_type'=>'text',
	'sort_order'=>'31',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');

$this->addConfigField('sales/new_order', 'New order options', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('sales/new_order/email_identity', 'Confirmation Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'sales');
$this->addConfigField('sales/new_order/email_identity', 'Confirmation Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'sales');
$this->addConfigField('sales/new_order/email_identity', 'Confirmation Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'sales');
$this->addConfigField('sales/new_order/email_template', 'Confirmation Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '2');
$this->addConfigField('sales/new_order/email_template', 'Confirmation Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '2');
$this->addConfigField('sales/new_order/email_template', 'Confirmation Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '2');
$this->addConfigField('sales/order_statuses', 'Order status labels', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('sales/order_statuses/fake', 'Order status labels fake field', array(
	'frontend_type'=>'text',
	'frontend_model'=>'adminhtml/system_config_form_fieldset_order_statuses',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('sales/order_update', 'Order update options', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('sales/order_update/email_identity', 'Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'sales');
$this->addConfigField('sales/order_update/email_identity', 'Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'sales');
$this->addConfigField('sales/order_update/email_identity', 'Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'sales');
$this->addConfigField('sales/order_update/email_template', 'Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '4');
$this->addConfigField('sales/order_update/email_template', 'Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '4');
$this->addConfigField('sales/order_update/email_template', 'Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '4');
$this->addConfigField('sales/totals_sort', 'Checkout totals sort order', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('sales/totals_sort/discount', 'Discount', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '20');
$this->addConfigField('sales/totals_sort/discount', 'Discount', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '20');
$this->addConfigField('sales/totals_sort/discount', 'Discount', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '20');
$this->addConfigField('sales/totals_sort/grand_total', 'Grand Total', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '100');
$this->addConfigField('sales/totals_sort/grand_total', 'Grand Total', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '100');
$this->addConfigField('sales/totals_sort/grand_total', 'Grand Total', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '100');
$this->addConfigField('sales/totals_sort/shipping', 'Shipping', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '30');
$this->addConfigField('sales/totals_sort/shipping', 'Shipping', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '30');
$this->addConfigField('sales/totals_sort/shipping', 'Shipping', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '30');
$this->addConfigField('sales/totals_sort/subtotal', 'Subtotal', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '10');
$this->addConfigField('sales/totals_sort/subtotal', 'Subtotal', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '10');
$this->addConfigField('sales/totals_sort/subtotal', 'Subtotal', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '10');
$this->addConfigField('sales/totals_sort/tax', 'Tax', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '40');
$this->addConfigField('sales/totals_sort/tax', 'Tax', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '40');
$this->addConfigField('sales/totals_sort/tax', 'Tax', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '40');
