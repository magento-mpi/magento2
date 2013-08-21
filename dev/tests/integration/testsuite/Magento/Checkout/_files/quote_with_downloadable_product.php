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

require __DIR__ . '/../../../Magento/Downloadable/_files/product.php';

/** @var $product Magento_Catalog_Model_Product */
$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->load(1);

/** @var $linkCollection Magento_Downloadable_Model_Resource_Link_Collection */
$linkCollection = Mage::getModel('Magento_Downloadable_Model_Link')->getCollection()
    ->addProductToFilter($product->getId())
    ->addTitleToResult($product->getStoreId())
    ->addPriceToResult($product->getStore()->getWebsiteId());

/** @var $link Magento_Downloadable_Model_Link */
$link = $linkCollection->getFirstItem();

$requestInfo = new Magento_Object(array(
    'qty' => 1,
    'links' => array($link->getId())
));

/** @var $cart Magento_Checkout_Model_Cart */
$cart = Mage::getModel('Magento_Checkout_Model_Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

Mage::unregister('_singleton/Magento_Checkout_Model_Session');

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Mage::getObjectManager();
$objectManager->removeSharedInstance('Magento_Checkout_Model_Session');
