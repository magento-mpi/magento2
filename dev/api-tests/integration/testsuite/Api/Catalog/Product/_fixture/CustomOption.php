<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$fixture = simplexml_load_file(__DIR__ . '/_data/xml/CustomOption.xml');

//Create new simple product
$productData = Magento_Test_Webservice::simpleXmlToArray($fixture->fixtureProduct);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = new Mage_Catalog_Model_Product();
$product->setData($productData)->setStoreId(1)->save();
Magento_Test_Webservice::setFixture('productData', $product, Magento_Test_Webservice::AUTO_TEAR_DOWN_AFTER_CLASS);
