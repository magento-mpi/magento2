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

$fixture = simplexml_load_file(dirname(__FILE__).'/xml/CustomOption.xml');

//Create new simple product
$productData = Magento_Test_Webservice::simpleXmlToArray($fixture->fixtureProduct);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = new Mage_Catalog_Model_Product();
$product->setData($productData)->setStoreId(1)->save();
Magento_Test_Webservice::setFixture('productData', $product);
