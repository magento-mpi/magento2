<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

//Add customer
$fixture = simplexml_load_file(__DIR__ . '/_data/xml/LinkCRUD.xml');
$customerData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->customer);
$customerData['email'] = mt_rand(1000, 9999) . '.' . $customerData['email'];

$customer = Mage::getModel('Magento_Customer_Model_Customer');
$customer->setData($customerData)->save();
/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->register('customerData', $customer);

//Create new downloadable product
$productData = Magento_Test_Helper_Api::simpleXmlToArray($fixture->product);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->setData($productData)->save();
$objectManager->get('Magento_Core_Model_Registry')->register('productData', $product);
