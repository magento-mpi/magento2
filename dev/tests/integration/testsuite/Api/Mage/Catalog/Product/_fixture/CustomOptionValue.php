<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$fixture = simplexml_load_file(__DIR__ . '/_data/xml/CustomOptionValue.xml');

//Create new simple product
$productData = Magento_Test_Helper_Api::simpleXmlToObject($fixture->fixtureProduct);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setData($productData)->save();
PHPUnit_Framework_TestCase::setFixture(
    'productData',
    $product,
    PHPUnit_Framework_TestCase::AUTO_TEAR_DOWN_DISABLED
);

$customOptionApi = Mage::getModel('Mage_Catalog_Model_Product_Option_Api');
$data = Magento_Test_Helper_Api::simpleXmlToObject($fixture->fixtureCustomOption);
// unsetOptions() call helps to prevent duplicate options add
// during the sequence of $customOptionApi->add() calls in unit test suite
Mage::getSingleton('Mage_Catalog_Model_Product_Option')->unsetOptions();
$customOptionApi->add($product->getId(), $data);
$customOptionsList = $customOptionApi->items($product->getId());

PHPUnit_Framework_TestCase::setFixture('customOptionId', $customOptionsList[0]['option_id']);
