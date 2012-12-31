<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

//Add customer
$fixture = simplexml_load_file(__DIR__ . '/_data/xml/LinkCRUD.xml');
$customerData = Magento_Test_Helper_Api::simpleXmlToObject($fixture->customer);
$customerData['email'] = mt_rand(1000, 9999) . '.' . $customerData['email'];

$customer = Mage::getModel('Mage_Customer_Model_Customer');
$customer->setData($customerData)->save();
PHPUnit_Framework_TestCase::setFixture(
    'customerData',
    $customer,
    PHPUnit_Framework_TestCase::AUTO_TEAR_DOWN_DISABLED
);

//Create new downloadable product
$productData = Magento_Test_Helper_Api::simpleXmlToObject($fixture->product);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setData($productData)->save();
PHPUnit_Framework_TestCase::setFixture(
    'productData',
    $product,
    PHPUnit_Framework_TestCase::AUTO_TEAR_DOWN_DISABLED
);
