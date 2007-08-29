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
replace into `core_email_template` (`template_id`,`template_code`,`template_text`,`template_type`,`template_subject`,`template_sender_name`,`template_sender_email`,`added_at`,`modified_at`) values (6,'Newsletter subscription confirmation (HTML)','Hello,<br />\r\n<br />\r\nThank you for subscribing to our newsletter.<br />\r\n<br />\r\nTo begin receiving the newsletter, you must first confirm your subscription by clicking on the following link:<br />\r\n<a href=\"{{var subscriber.getConfirmationLink()}}\">{{var subscriber.getConfirmationLink()}}</a><br /><br />\r\n\r\nThanks again!<br />\r\nSincerely,<br />\r\n\r\nMagento Store.',2,'Newsletter subscription confirmation',NULL,NULL,'2007-08-16 18:31:57','2007-08-29 08:56:53');