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

$requestInfo = new Varien_Object(array(
    'qty' => 1,
    'links' => array($link->getId())
));

require __DIR__ . '/../../Checkout/_files/cart.php';
