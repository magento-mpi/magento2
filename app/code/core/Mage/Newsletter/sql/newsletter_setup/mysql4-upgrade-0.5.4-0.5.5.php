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
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$conn->multi_query(<<<EOT

delete from core_config_field where path like 'email%';
delete from core_config_field where path like 'trans_email/trans%';
delete from core_config_field where path like 'newsletter%';

EOT
);

$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');

$this->addConfigField('newsletter', 'Newsletter');
$this->addConfigField('newsletter/subscription', 'Subscription Options');
$this->addConfigField('newsletter/subscription/confirm', 'Need Confirmation', array(
    'frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_yesno'
));
$this->addConfigField('newsletter/subscription/confirm_email_identity', 'Confirmation Email Sender', $identity);
$this->addConfigField('newsletter/subscription/confirm_email_template', 'Confirmation Email Template', $template);
$this->addConfigField('newsletter/subscription/success_email_identity', 'Success Email Sender', $identity);
$this->addConfigField('newsletter/subscription/success_email_template', 'Success Email Template', $template);
$this->addConfigField('newsletter/subscription/un_email_identity', 'Unsubscription Email Sender', $identity);
$this->addConfigField('newsletter/subscription/un_email_template', 'Unsubscription Email Template', $template);
