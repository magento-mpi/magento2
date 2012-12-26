<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$productData = require '_fixture/_data/Catalog/Product/Simple/simple_product_data.php';
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->addData($productData)
    ->setStoreId(0)
    ->setPrice(10)
    ->setTaxClassId(2)
    ->setStockData(array('use_config_manage_stock' => 1))
    ->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId()))
    ->save();

Magento_Test_TestCase_ApiAbstract::setFixture('product_simple_taxes', $product);
