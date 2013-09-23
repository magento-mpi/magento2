<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Catalog/_files/products.php';

/** @var $product Magento_Catalog_Model_Product */
$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
$product->load(1);

$requestInfo = new Magento_Object(array(
    'qty' => 1
));

/** @var $cart Magento_Checkout_Model_Cart */
$cart = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Checkout_Model_Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->unregister('_singleton/Magento_Checkout_Model_Session');

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->removeSharedInstance('Magento_Checkout_Model_Session');
