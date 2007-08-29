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


$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');

$this->addConfigField('trans_email/trans_subscription_success', 'Transactional email - Newsletter subscription success');
$this->addConfigField('trans_email/trans_subscription_success/identity', 'Sender', $identity);
$this->addConfigField('trans_email/trans_subscription_success/template', 'Template', $template);


$this->addConfigField('customer/account', 'Account options');
$this->addConfigField('customer/account/confirm', 'Request new account confirmation', array(
	'frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_yesno'
));

$this->addConfigField('customer/newsletter', 'Newsletter options');
$this->addConfigField('customer/newsletter/confirm', 'Request new subscription confirmation', array(
	'frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_yesno'
));

$conn->raw_query("update core_config_field set frontend_type='select', source_model='adminhtml/system_config_source_web_protocol' where path like 'web/%/protocol'");

$this->addConfigField('design/package/translate', 'Translation theme');
$this->setConfigData('design/package/translate', 'default');

$this->addConfigField('design/package/default_theme', 'Default theme');
$this->setConfigData('design/package/default_theme', 'default');