<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$fixture = simplexml_load_file(__DIR__ . '/_data/xml/CustomOptionValue.xml');

//Create new simple product
$productData = Magento_Test_TestCase_ApiAbstract::simpleXmlToArray($fixture->fixtureProduct);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setData($productData)->save();
Magento_Test_TestCase_ApiAbstract::setFixture(
    'productData',
    $product,
    Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
);

$customOptionApi = Mage::getModel('Mage_Catalog_Model_Product_Option_Api');
$data = Magento_Test_TestCase_ApiAbstract::simpleXMLToArray($fixture->fixtureCustomOption);
// unsetOptions() call helps to prevent duplicate options add
// during the sequence of $customOptionApi->add() calls in unit test suite
Mage::getSingleton('Mage_Catalog_Model_Product_Option')->unsetOptions();
$customOptionApi->add($product->getId(), $data);
$customOptionsList = $customOptionApi->items($product->getId());

Magento_Test_TestCase_ApiAbstract::setFixture('customOptionId', $customOptionsList[0]['option_id']);
