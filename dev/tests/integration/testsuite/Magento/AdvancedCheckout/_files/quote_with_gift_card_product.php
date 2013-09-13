<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/GiftCard/_files/gift_card.php';

/** @var $product Magento_Catalog_Model_Product */
$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->load(1);

$requestInfo = new Magento_Object(array(
    'qty' => 1,
    'giftcard_amount'         => 'custom',
    'custom_giftcard_amount'  => 200,
    'giftcard_sender_name'    => 'Sender',
    'giftcard_sender_email'   => 'aerfg@sergserg.com',
    'giftcard_recipient_name' => 'Recipient',
    'giftcard_recipient_email'=> 'awefaef@dsrthb.com',
    'giftcard_message'        => 'message'
));

/** @var $cart Magento_Checkout_Model_Cart */
$cart = Mage::getModel('Magento_Checkout_Model_Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')
    ->unregister('_singleton/Magento_Checkout_Model_Session');

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->removeSharedInstance('Magento_Checkout_Model_Session');
