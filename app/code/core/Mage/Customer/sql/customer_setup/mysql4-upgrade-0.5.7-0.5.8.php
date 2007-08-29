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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$conn->multi_query(<<<EOT

delete from core_config_field where path like 'customer%';

EOT
);


$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');


$this->addConfigField('customer', 'Customers');

$this->addConfigField('customer/create_account', 'Create New Account Options');
$this->addConfigField('customer/create_account/default_group', 'Default Group', array(
    'frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_customer_group'
));
$this->addConfigField('customer/create_account/confirm', 'Need Confirmation', array(
    'frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_yesno'
));
$this->addConfigField('customer/create_account/email_identity', 'Email Sender', $identity);
$this->addConfigField('customer/create_account/email_template', 'Email Template', $template);


$this->addConfigField('customer/password', 'Password Options');
$this->addConfigField('customer/password/forgot_email_identity', 'Forgot Email Sender', $identity);
$this->addConfigField('customer/password/forgot_email_template', 'Forgot Email Template', $template);
