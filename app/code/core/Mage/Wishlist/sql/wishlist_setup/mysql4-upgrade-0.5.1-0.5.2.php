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
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$this->addConfigField('wishlist/email', 'Share options');

$this->addConfigField('wishlist/email/email_identity', 'Email Sender', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_identity',
));

$this->addConfigField('wishlist/email/email_template', 'Email Template', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_email_template',
));

$this->run(
<<<EOT
DELETE FROM `core_email_template` WHERE `template_code`='Share Wishlist';
INSERT INTO `core_email_template` (`template_id`, `template_code`, `template_text`, `template_type`, `template_subject`, `template_sender_name`, `template_sender_email`, `added_at`, `modified_at`) VALUES 
(NULL, 'Share Wishlist', '{{var message}}<br>\r\n\r\n{{var items}}<br>\r\n\r\n<a href="{{var addAllLink}}">Add all items to cart</a><br>\r\n<a href="{{var viewOnSiteLink}}">View this items on site</a>', 2, 'Share Wishlist Subject', NULL, NULL, '2007-08-25 19:27:49', '2007-08-26 15:58:43');
EOT
);

$template = $conn->raw_fetchRow("select template_id from `core_email_template` where template_code='Share Wishlist'");
$this->setConfigData('wishlist/email/email_template', $template['template_id']);