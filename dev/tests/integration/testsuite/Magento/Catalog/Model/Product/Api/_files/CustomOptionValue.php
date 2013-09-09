<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

$fixture = simplexml_load_file(__DIR__ . '/_data/xml/CustomOptionValue.xml');

//Create new simple product
$productData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->fixtureProduct);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->setData($productData)->save();
/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->register('productData', $product);

$customOptionApi = Mage::getModel('Magento_Catalog_Model_Product_Option_Api');
$data = Magento_Test_Helper_Api::simpleXmlToArray($fixture->fixtureCustomOption);
// unsetOptions() call helps to prevent duplicate options add
// during the sequence of $customOptionApi->add() calls in unit test suite
Mage::getSingleton('Magento_Catalog_Model_Product_Option')->unsetOptions();
$customOptionApi->add($product->getId(), $data);
$customOptionsList = $customOptionApi->items($product->getId());
$objectManager->get('Magento_Core_Model_Registry')->register('customOptionId', $customOptionsList[0]['option_id']);
