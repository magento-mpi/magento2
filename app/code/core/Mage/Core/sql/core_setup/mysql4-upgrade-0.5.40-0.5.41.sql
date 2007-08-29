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
UPDATE `core_config_field` SET `sort_order` = '900' WHERE `path` ='dev';

UPDATE `core_config_field` SET `path` = 'newsletter/subscription_un_email/template', `frontend_label` = 'Template' WHERE `path` = 'newsletter/subscription/un_email_template';
UPDATE `core_config_field` SET `path` = 'newsletter/subscription_un_email/identity', `frontend_label` = 'Sender' WHERE `path` = 'newsletter/subscription/un_email_identity';
UPDATE `core_config_field` SET `path` = 'newsletter/subscription_success_email/template', `frontend_label` = 'Template' WHERE `path` = 'newsletter/subscription/success_email_template';
UPDATE `core_config_field` SET `path` = 'newsletter/subscription_success_email/identity', `frontend_label` = 'Sender' WHERE `path` = 'newsletter/subscription/success_email_identity';
UPDATE `core_config_field` SET `path` = 'newsletter/subscription_confirm_email/template', `frontend_label` = 'Template' WHERE `path` = 'newsletter/subscription/confirm_email_template';
UPDATE `core_config_field` SET `path` = 'newsletter/subscription_confirm_email/identity', `frontend_label` = 'Sender' WHERE `path` = 'newsletter/subscription/confirm_email_identity';

REPLACE INTO `core_config_field` set `level` = 2, `path` = 'newsletter/subscription_confirm_email', `frontend_label` = 'Confirmation Email', `sort_order` = 1;
REPLACE INTO `core_config_field` set `level` = 2, `path` = 'newsletter/subscription_success_email', `frontend_label` = 'Success Email', `sort_order` = 2;
REPLACE INTO `core_config_field` set `level` = 2, `path` = 'newsletter/subscription_un_email', `frontend_label` = 'Unsubscription Email', `sort_order` = 3;
