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
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer->setConfigData('payment/ccsave/active', '1');
$installer->setConfigData('payment/ccsave/cctypes', 'VI,MC');
$installer->setConfigData('payment/ccsave/model', 'payment/method_ccsave');
$installer->setConfigData('payment/ccsave/order_status', 'processing');
$installer->setConfigData('payment/ccsave/sort_order', '1');
$installer->setConfigData('payment/ccsave/title', 'Credit Card');

$installer->setConfigData('payment/checkmo/active', '1');
$installer->setConfigData('payment/checkmo/model', 'payment/method_checkmo');
$installer->setConfigData('payment/checkmo/order_status', 'processing');
$installer->setConfigData('payment/checkmo/sort_order', '2');
$installer->setConfigData('payment/checkmo/title', 'Check / Money order');

$installer->setConfigData('payment/purchaseorder/active', '1');
$installer->setConfigData('payment/purchaseorder/model', 'payment/method_purchaseorder');
$installer->setConfigData('payment/purchaseorder/order_status', 'processing');
$installer->setConfigData('payment/purchaseorder/sort_order', '3');
$installer->setConfigData('payment/purchaseorder/title', 'Purchase Order');