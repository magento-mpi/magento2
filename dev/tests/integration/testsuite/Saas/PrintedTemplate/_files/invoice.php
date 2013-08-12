<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return; // MAGETWO-7075

$addressData = include(__DIR__ . '/order.php');

$invoice = Mage::getModel('Magento_Sales_Model_Order_Invoice')
    ->setIncrementId('100000001')
    ->setShippingAmount('1.00')
    ->setOrder($order);

$invoice->save();
