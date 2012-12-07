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
$productData = require TEST_FIXTURE_DIR . '/_data/Catalog/Product/Simple/simple_product_data.php';
$product = new Mage_Catalog_Model_Product();
$product->addData($productData)
    ->setStoreId(0)
    ->setPrice(10)
    ->setTaxClassId(2)
    ->setStockData(array('use_config_manage_stock' => 1))
    ->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId()))
    ->save();

Magento_Test_Webservice::setFixture('product_simple_taxes', $product);
