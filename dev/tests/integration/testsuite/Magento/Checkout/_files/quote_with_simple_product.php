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

/** @var $product \Magento\Catalog\Model\Product */
$product = Mage::getModel('Magento\Catalog\Model\Product');
$product->load(1);

$requestInfo = new \Magento\Object(array(
    'qty' => 1
));

/** @var $cart \Magento\Checkout\Model\Cart */
$cart = Mage::getModel('Magento\Checkout\Model\Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

Mage::unregister('_singleton/\Magento\Checkout\Model\Session');

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->removeSharedInstance('\Magento\Checkout\Model\Session');
