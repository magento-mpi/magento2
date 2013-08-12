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

$creditmemo = Mage::getModel('Magento_Sales_Model_Order_Creditmemo')
    ->setShippingAmount('1.00')
    ->setOrder($order);

$creditmemo->save();
