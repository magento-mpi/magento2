<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (Magento_Test_Helper_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
    return; // MAGETWO-7075
}

$addressData = include(__DIR__ . '/order.php');

$creditmemo = Mage::getModel('Mage_Sales_Model_Order_Creditmemo')
    ->setShippingAmount('1.00')
    ->setOrder($order);

$creditmemo->save();
