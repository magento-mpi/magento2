<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../../fixture');

/* @var $productFixture Mage_Catalog_Model_Product */
$product = require $fixturesDir . '/Catalog/Product.php';
$product->setStoreId(0)->save();

// to make stock item visible from created product it should be reloaded
$product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
Magento_Test_Webservice::setFixture('product_simple', $product);
