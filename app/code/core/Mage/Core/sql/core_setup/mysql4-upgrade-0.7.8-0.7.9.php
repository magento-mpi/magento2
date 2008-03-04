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

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$configValuesMap = array(
    'customer/create_account/email_template'            =>  'customer_create_account_email_template',
    'customer/password_forgot/email_template'           =>  'customer_password_forgot_email_template',
    'sales/new_order/email_template'                    =>  'sales_new_order_email_template',
    'sales/email/invoice_comment_template'              =>  'sales_email_invoice_comment_template',
    'sales/email/creditmemo_comment_template'           =>  'sales_email_creditmemo_comment_template',
    'sales/email/shipment_comment_template'             =>  'sales_email_shipment_comment_template',
    'sales/order_update/email_template'                 =>  'sales_order_update_email_template',
    'newsletter/subscription/confirm_email_template'    =>  'newsletter_subscription_confirm_email_template',
    'newsletter/subscription/success_email_template'    =>  'newsletter_subscription_success_email_template',
    'newsletter/subscription/un_email_template'         =>  'newsletter_subscription_un_email_template',
    'wishlist/email/email_template'                     =>  'wishlist_email_email_template',
    'sendfriend/email/template'                         =>  'sendfriend_email_template',
    'sitemap/generate/error_email_template'             =>  'sitemap_generate_error_email_template',
    'catalog/productalert/email_stock_template'         =>  'catalog_productalert_email_stock_template',
    'catalog/productalert/email_price_template'         =>  'catalog_productalert_email_price_template',
    'contacts/email/email_template'                     =>  'contacts_email_email_template',
    'currency/import/error_email_template'              =>  'currency_import_error_email_template'
);

$templatesCodes = array(
    'New account (HTML)',
    'New order (HTML)',
    'New password (HTML)',
    'Order update (HTML)',
    'New account (Plain)',
    'Newsletter subscription confirmation (HTML)',
    'Share Wishlist',
    'Newsletter Subscription Success',
    'Newsletter Unsubscription Success',
    'Send product to a friend',
    'Currency Update Warnings',
    'Contact Form (Plain)',
    'Sitemap generate Warnings',
    'Product price alert',
    'Product stock alert'
);

foreach ($configValuesMap as $configPath=>$configValue) {
    $installer->setConfigData($configPath, $configValue);
}

$installer->startSetup();
$installer->run(
    "DELETE FROM `{$installer->getTable('core_email_template')}`
        WHERE {$installer->getConnection()->quoteInto('`template_code` IN(?)', $templatesCodes)};"
);
$installer->endSetup();
