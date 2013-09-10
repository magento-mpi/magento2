<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

//Add customer
$tagFixture = simplexml_load_file(__DIR__ . '/_data/xml/TagCRUD.xml');
$customerData = Magento_TestFramework_Helper_Api::simpleXmlToArray($tagFixture->customer);
$customerData['email'] = mt_rand(1000, 9999) . '.' . $customerData['email'];

$customer = Mage::getModel('Magento_Customer_Model_Customer');
$customer->setData($customerData)->save();
/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->register('customerData', $customer);

//Create new simple product
$productData = Magento_TestFramework_Helper_Api::simpleXmlToArray($tagFixture->product);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->setData($productData)->save();
$objectManager->get('Magento_Core_Model_Registry')->register('productData', $product);
