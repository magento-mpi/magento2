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

$fixture = simplexml_load_file(dirname(__FILE__).'/xml/CustomOptionValue.xml');

//Create new simple product
$productData = Magento_Test_Webservice::simpleXmlToArray($fixture->fixtureProduct);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = new Mage_Catalog_Model_Product();
$product->setData($productData)->save();
Magento_Test_Webservice::setFixture('productData', $product);

$customOptionApi = Mage::getModel('catalog/product_option_api');
$data = Magento_Test_Webservice::simpleXMLToArray($fixture->fixtureCustomOption);
// unsetOptions() call helps to prevent duplicate options add
// during the sequence of $customOptionApi->add() calls in unit test suite
Mage::getSingleton('catalog/product_option')->unsetOptions();
$customOptionApi->add($product->getId(), $data);
$customOptionsList = $customOptionApi->items($product->getId());

Magento_Test_Webservice::setFixture('customOptionId', $customOptionsList[0]['option_id']);
