<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $billingAddress Magento_Sales_Model_Order_Address */
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

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

/** @var $payment Magento_Sales_Model_Order_Payment */
$payment = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Payment');
$payment->setMethod('checkmo');

/** @var $orderItem Magento_Sales_Model_Order_Item */
$orderItem = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Item');
$orderItem->setProductId(1)
    ->setProductType(Magento_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD)
    ->setBasePrice(100)
    ->setQtyOrdered(1)
    ->setProductOptions(array(
        'giftcard_amount'         => 'custom',
        'custom_giftcard_amount'  => 100,
        'giftcard_sender_name'    => 'Gift Card Sender Name',
        'giftcard_sender_email'   => 'sender@example.com',
        'giftcard_recipient_name' => 'Gift Card Recipient Name',
        'giftcard_recipient_email'=> 'recipient@example.com',
        'giftcard_message'        => 'Gift Card Message',
        'giftcard_email_template' => 'giftcard_email_template',
    ));

/** @var $order Magento_Sales_Model_Order */
$order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
$order->addItem($orderItem)
    ->setIncrementId('100000001')
    ->setCustomerIsGuest(true)
    ->setStoreId(1)
    ->setEmailSent(1)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setPayment($payment);
$order->save();

Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->get('Magento_Core_Model_Config')
    ->setNode('websites/base/giftcard/giftcardaccount_general/pool_size', 1);
/** @var $pool Magento_GiftCardAccount_Model_Pool */
$pool = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_GiftCardAccount_Model_Pool');
$pool->setWebsiteId(1)->generatePool();
