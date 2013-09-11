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

$customer = Mage::getModel('\Magento\Customer\Model\Customer');
$customer->setData($customerData)->save();
Mage::register('customerData', $customer);

//Create new simple product
$productData = Magento_TestFramework_Helper_Api::simpleXmlToArray($tagFixture->product);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = Mage::getModel('\Magento\Catalog\Model\Product');
$product->setData($productData)->save();
Mage::register('productData', $product);
