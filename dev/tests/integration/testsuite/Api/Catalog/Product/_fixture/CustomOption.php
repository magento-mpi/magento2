<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$fixture = simplexml_load_file(__DIR__ . '/_data/xml/CustomOption.xml');

//Create new simple product
$productData = Magento_Test_TestCase_ApiAbstract::simpleXmlToArray($fixture->fixtureProduct);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setData($productData)->setStoreId(1)->save();
Magento_Test_TestCase_ApiAbstract::setFixture(
    'productData',
    $product,
    Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_AFTER_CLASS
);
