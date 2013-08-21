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

require __DIR__ . '/../../../Magento/Bundle/_files/product.php';

/** @var $product Magento_Catalog_Model_Product */
$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->load(3);

/** @var $typeInstance Magento_Bundle_Model_Product_Type */
//Load options
$typeInstance = $product->getTypeInstance();
$typeInstance->setStoreFilter($product->getStoreId(), $product);
$optionCollection = $typeInstance->getOptionsCollection($product);
$selectionCollection = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);

$bundleOptions = array();
$bundleOptionsQty = array();
/** @var $option Magento_Bundle_Model_Option */
foreach ($optionCollection as $option) {
    /** @var $selection Magento_Bundle_Model_Selection */
    $selection = $selectionCollection->getFirstItem();
    $bundleOptions[$option->getId()] = $selection->getSelectionId();
    $bundleOptionsQty[$option->getId()] = 1;
}

$requestInfo = new Magento_Object(array(
    'qty' => 1,
    'bundle_option' => $bundleOptions,
    'bundle_option_qty' => $bundleOptionsQty
));

/** @var $cart Magento_Checkout_Model_Cart */
$cart = Mage::getModel('Magento_Checkout_Model_Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

Mage::unregister('_singleton/Magento_Checkout_Model_Session');

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Mage::getObjectManager();
$objectManager->removeSharedInstance('Magento_Checkout_Model_Session');
