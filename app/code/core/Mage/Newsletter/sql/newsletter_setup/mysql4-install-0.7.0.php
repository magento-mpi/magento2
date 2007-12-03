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

$this->addConfigField('newsletter', 'Newsletter', array(
	'frontend_type'=>'text',
	'sort_order'=>'31',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('newsletter/subscription', 'Subscription Options', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('newsletter/subscription/confirm', 'Need to Confirm', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('newsletter/subscription/confirm_email_identity', 'Confirmation Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'support');
$this->addConfigField('newsletter/subscription/confirm_email_template', 'Confirmation Email Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '6');
$this->addConfigField('newsletter/subscription/success_email_identity', 'Success Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'general');
$this->addConfigField('newsletter/subscription/success_email_template', 'Success Email Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '8');
$this->addConfigField('newsletter/subscription/un_email_identity', 'Unsubscription Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'support');
$this->addConfigField('newsletter/subscription/un_email_template', 'Unsubscription Email Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '9');
