<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*
 * Since the bundle product creation GUI doesn't allow to choose values for bundled products' custom options,
 * bundled items should not contain products with required custom options.
 * However, if to create such a bundle product, it will be always out of stock.
 */
require __DIR__ . '/../../../Magento/Catalog/_files/products.php';

/** @var $product Magento_Catalog_Model_Product */
$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Product');
$product->setTypeId('bundle')
    ->setId(3)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Bundle Product')
    ->setSku('bundle-product')
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setStockData(array(
        'use_config_manage_stock'   => 1,
        'qty'                       => 100,
        'is_qty_decimal'            => 0,
        'is_in_stock'               => 1,
    ))
    ->setBundleOptionsData(array(
        array(
            'title'    => 'Bundle Product Items',
            'type'     => 'select',
            'required' => 1,
            'delete'   => '',
        ),
    ))
    ->setBundleSelectionsData(array(
        array(
            array(
                'product_id'               => 1, // fixture product
                'selection_qty'            => 1,
                'selection_can_change_qty' => 1,
                'delete'                   => '',
            ),
        ),
    ))
    ->save()
;
