<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../Catalog/_files/products.php';

/** @var $product Mage_Catalog_Model_Product */
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->load(1);

$requestInfo = new Magento_Object(array(
    'qty' => 1
));

/** @var $cart Mage_Checkout_Model_Cart */
$cart = Mage::getModel('Mage_Checkout_Model_Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

Mage::unregister('_singleton/Mage_Checkout_Model_Session');

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Mage::getObjectManager();
$objectManager->removeSharedInstance('Mage_Checkout_Model_Session');
