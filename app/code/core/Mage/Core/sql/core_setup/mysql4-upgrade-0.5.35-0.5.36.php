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


$this->addConfigField('web/cookie', 'Cookie management');
$this->addConfigField('web/cookie/cookie_domain', 'Cookie Domain');
$this->addConfigField('web/cookie/cookie_path', 'Cookie Path');
$this->addConfigField('web/cookie/cookie_lifetime', 'Cookie Lifetime');

$identity = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_identity');
$template = array('frontend_type'=>'select', 'source_model'=>'adminhtml/system_config_source_email_template');

$this->addConfigField('trans_email/trans_new_subscription', 'Transactional email - Newsletter subscription');
$this->addConfigField('trans_email/trans_new_subscription/identity', 'Sender', $identity);
$this->addConfigField('trans_email/trans_new_subscription/template', 'Template', $template);