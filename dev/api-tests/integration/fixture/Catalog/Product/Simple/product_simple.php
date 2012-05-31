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
$productData = require TEST_FIXTURE_DIR . '/_data/Catalog/Product/Simple/SimpleProductData.php';
$product = new Mage_Catalog_Model_Product();
$product->setStoreId(0)
    ->setStockData(array(
        'use_config_manage_stock' => 0,
        'manage_stock' => 1,
        'qty' => 500,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    ))
    ->setTierPrice(
        array(
            array(
                'website_id' => 0,
                'cust_group' => Mage_Customer_Model_Group::CUST_GROUP_ALL,
                'price_qty' => 2,
                'price' => 95,
            ),
            array(
                'website_id' => 0,
                'cust_group' => 1, // General customer group
                'price_qty' => 5,
                'price' => 90,
            ),
            array(
                'website_id' => 0,
                'cust_group' => 0, // Not logged in customer group
                'price_qty' => 5,
                'price' => 93,
            ),
        )
    )
    ->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId()));

$product->addData($productData)->save();

// to make stock item visible from created product it should be reloaded
$product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
Magento_Test_Webservice::setFixture('product_simple', $product);
