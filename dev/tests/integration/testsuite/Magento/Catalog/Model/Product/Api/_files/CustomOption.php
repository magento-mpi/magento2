<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

$fixture = simplexml_load_file(__DIR__ . '/_data/xml/CustomOption.xml');

//Create new simple product
$productData = Magento_TestFramework_Helper_Api::simpleXmlToArray($fixture->fixtureProduct);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999);
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999);

$product = Mage::getModel('Magento\Catalog\Model\Product');
$product->setData($productData)->setStoreId(1)->save();
Mage::register('productData', $product);
