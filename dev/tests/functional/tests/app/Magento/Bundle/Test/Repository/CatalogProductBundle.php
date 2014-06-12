<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogProductBundle
 * Data for creation Catalog Product Bundle
 */
class CatalogProductBundle extends AbstractRepository
{
    /**
     * Constructor
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['BundleDynamic_sku_1073507449'] = [
            'sku' => 'BundleDynamic_sku_10735074493',
            'name' => 'BundleDynamic 1073507449',
            'price' => [
                'price_from' => 1,
                'price_to' => 2
            ],
            'short_description' => '',
            'description' => '',
            'tax_class_id' => '2',
            'sku_type' => '0',
            'price_type' => '0',
            'weight_type' => '0',
            'status' => '1',
            'shipment_type' => '1',
            'mtf_dataset_name' => 'BundleDynamic_sku_1073507449'
        ];

        $this->_data['BundleDynamic_sku_215249172'] = [
            'sku' => 'BundleDynamic_sku_215249172',
            'name' => 'BundleDynamic 215249172',
            'price' => [
                'price_from' => 3,
                'price_to' => 4
            ],
            'short_description' => '',
            'description' => '',
            'tax_class_id' => '2',
            'sku_type' => '0',
            'weight_type' => '0',
            'price_type' => '0',
            'shipment_type' => '1',
            'mtf_dataset_name' => 'BundleDynamic_sku_215249172'
        ];

        $this->_data['bundle_dynamic_product'] = [
            'name' => 'bundle_dynamic_product%isolation%',
            'sku' => 'bundle_dynamic_product%isolation%',
            'sku_type' => 'Dynamic',
            'price_type' => 'Dynamic',
            'quantity_and_stock_status' => 'In Stock',
            'weight_type' => 'Dynamic',
            'shipment_type' => 'Separately',
            'tax_class_id' => 'None',
            'website_ids' => [
                'Main Website',
            ],
            'stock_data' => [
                'manage_stock' => 'Yes',
                'use_config_enable_qty_increments' => 'Yes',
                'use_config_qty_increments' => 'Yes',
                'is_in_stock' => 'In Stock'
            ],
            'url_key' => 'bundle-dynamic-product-%isolation%',
            'visibility' => 'Catalog, Search',
            'bundle_option' => [
                [
                    'title' => 'Bundle Options title%isolation%',
                    'type' => 'Drop-down',
                    'required' => 'Yes',
                    'position' => 0,
                ],
            ],
            'bundle_selection' => [
                [
                    0 => [
                        'product_id' => '%simple_for_composite_products%',
                        'selection_qty' => 1,
                        'selection_price_value' => '10.00',
                        'selection_price_type' => 0,
                        'selection_can_change_qty' => 1,
                        'position' => 0,
                    ],
                ],
            ],
            'attribute_set_id' => 'Default',
        ];

        $this->_data['bundle_fixed_product'] = [
            'name' => 'bundle_fixed_product%isolation%',
            'sku' => 'bundle_fixed_product%isolation%',
            'sku_type' => 'Fixed',
            'price_type' => 'Fixed',
            'price' => '40.00',
            'tax_class_id' => 'None',
            'quantity_and_stock_status' => 'In Stock',
            'weight' => '1.0000',
            'weight_type' => 'Fixed',
            'status' => '1',
            'shipment_type' => 'Together',
            'website_ids' => [
                'Main Website',
            ],
            'stock_data' => [
                'manage_stock' => 'Yes',
                'use_config_enable_qty_increments' => 'Yes',
                'use_config_qty_increments' => 'Yes',
                'is_in_stock' => 'In Stock'
            ],
            'url_key' => 'bundle-dynamic-product%isolation%',
            'visibility' => 'Catalog, Search',
            'bundle_option' => [
                [
                    'title' => 'Bundle Options title',
                    'type' => 'Drop-down',
                    'required' => 'Yes',
                    'position' => '0'
                ],
            ],
            'bundle_selection' => [
                [
                    0 => [
                        'product_id' => '%simple_for_composite_products%',
                        'selection_price_value' => '10.00',
                        'selection_price_type' => 0,
                        'selection_qty' => 1,
                        'selection_can_change_qty' => 'Yes',
                        'position' => 0
                    ],
                ],
            ],
            'attribute_set_id' => 'Default',
        ];
    }
}
