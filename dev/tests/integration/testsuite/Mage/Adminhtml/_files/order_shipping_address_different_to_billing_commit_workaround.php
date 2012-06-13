<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require __DIR__ . '/order_shipping_address_different_to_billing.php';
$order->afterCommitCallback();
