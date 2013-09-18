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

/** @var $product \Magento\Catalog\Model\Product */
$product = Mage::getModel('Magento\Catalog\Model\Product');
$product->load(3);

/** @var $typeInstance \Magento\Bundle\Model\Product\Type */
//Load options
$typeInstance = $product->getTypeInstance();
$typeInstance->setStoreFilter($product->getStoreId(), $product);
$optionCollection = $typeInstance->getOptionsCollection($product);
$selectionCollection = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);

$bundleOptions = array();
$bundleOptionsQty = array();
/** @var $option \Magento\Bundle\Model\Option */
foreach ($optionCollection as $option) {
    /** @var $selection \Magento\Bundle\Model\Selection */
    $selection = $selectionCollection->getFirstItem();
    $bundleOptions[$option->getId()] = $selection->getSelectionId();
    $bundleOptionsQty[$option->getId()] = 1;
}

$requestInfo = new \Magento\Object(array(
    'qty' => 1,
    'bundle_option' => $bundleOptions,
    'bundle_option_qty' => $bundleOptionsQty
));

/** @var $cart \Magento\Checkout\Model\Cart */
$cart = Mage::getModel('Magento\Checkout\Model\Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento\Core\Model\Registry')->unregister('_singleton/Magento\Checkout\Model\Session');

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->removeSharedInstance('Magento\Checkout\Model\Session');
