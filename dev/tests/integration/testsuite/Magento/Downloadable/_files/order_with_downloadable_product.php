<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downlodable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$billingAddress = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Address',
    array(
        'data' => array(
            'firstname'  => 'guest',
            'lastname'   => 'guest',
            'email'      => 'customer@example.com',
            'street'     => 'street',
            'city'       => 'Los Angeles',
            'region'     => 'CA',
            'postcode'   => '1',
            'country_id' => 'US',
            'telephone'  => '1',
        )
    )
);
$billingAddress->setAddressType('billing');

$payment = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Payment');
$payment->setMethod('checkmo');

$orderItem = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Item');
$orderItem->setProductId(1)
    ->setProductType(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE)
    ->setBasePrice(100)
    ->setQtyOrdered(1);

$order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
$order->addItem($orderItem)
    ->setIncrementId('100000001')
    ->setCustomerIsGuest(true)
    ->setStoreId(1)
    ->setEmailSent(1)
    ->setBillingAddress($billingAddress)
    ->setPayment($payment);
$order->save();
