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

require __DIR__ . '/../../Downloadable/_files/product.php';

/** @var $product Mage_Catalog_Model_Product */
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->load(1);

/** @var $linkCollection Mage_Downloadable_Model_Resource_Link_Collection */
$linkCollection = Mage::getModel('Mage_Downloadable_Model_Link')->getCollection()
    ->addProductToFilter($product->getId())
    ->addTitleToResult($product->getStoreId())
    ->addPriceToResult($product->getStore()->getWebsiteId());

/** @var $link Mage_Downloadable_Model_Link */
$link = $linkCollection->getFirstItem();

$requestInfo = new Magento_Object(array(
    'qty' => 1,
    'links' => array($link->getId())
));

/** @var $cart Mage_Checkout_Model_Cart */
$cart = Mage::getModel('Mage_Checkout_Model_Cart');
$cart->addProduct($product, $requestInfo);
$cart->save();

Mage::unregister('_singleton/Mage_Checkout_Model_Session');

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$objectManager->removeSharedInstance('Mage_Checkout_Model_Session');
