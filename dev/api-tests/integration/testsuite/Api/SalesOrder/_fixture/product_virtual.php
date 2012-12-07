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

$product = new Mage_Catalog_Model_Product();
$product->setTypeId('virtual')
    ->setAttributeSetId(4)
    ->setStoreId(0)
    ->setName('Simple Product')
    ->setSku('virtual-creditmemo-' . uniqid())
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setStockData(
        array(
            'use_config_manage_stock' => 1,
            'qty'                     => 100,
            'is_qty_decimal'          => 0,
            'is_in_stock'             => 1,
        )
    );
    $product->save();
Magento_Test_Webservice::setFixture('product_virtual', $product, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
