<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogProductConfigurable
 * Data for creation Catalog Product Configurable
 */
class CatalogProductConfigurable extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'name' => 'Configurable Product %isolation%',
            'sku' => 'sku_configurable_product_%isolation%',
            'price' => ['value' => 100.00],
            'weight' => 1
        ];

        $this->_data['customDefault'] = [
            'name' => 'Test configurable product %isolation%',
            'sku' => 'sku_test_configurable_product_%isolation%',
            'price' => 120.00,
            'weight' => 30.0000,
            'status' => 1,
            'category_ids' => ['%category_ids%'],
            'visibility' => 'Catalog, Search',
            'url_key' => 'test-configurable-product-%isolation%',
            'attributes' => '%attributes_ids%',
            'configurable_attributes_data' => [
                '%attributes_id%' => [
                    'id' => 'new',
                    'code' => 'configurable_attribute',
                    'attribute_id' => '%attributes_id%',
                    'position' => 0,
                    'label' => 'configurableattribute',
                    'values' => [
                        '%attribute_value_id%' => [
                            'value_index' => '%attributes_values_id%',
                            'pricing_value' => 100.00,
                            'is_percent' => 0,
                            'include' => 1,
                        ],
                        'attribute_value_id' => [
                            'value_index' => '%attributes_values_id%',
                            'pricing_value' => 200.00,
                            'is_percent' => 0,
                            'include' => 1
                        ],
                    ]
                ]
            ],
            'variations-matrix' => [
                '%attributes_values_id%' => [
                    'name' => 'Test configurable simple product %isolation%',
                    'configurable_attribute' => '{"configurableattribute":"%attributes_values_id%"}',
                    'sku' => 'test_sku_configurable_simple_product_%isolation%',
                    'quantity_and_stock_status' => ['qty' => 120],
                    'weight' => '',
                ]
            ],
            'stock_data' => [
                'manage_stock' => 'No',
            ],
            'is_in_stock' => 'In Stock',
            'website_ids' => 'Main Website',
            'affect_configurable_product_attributes' => 1,
            'new-variations-attribute-set-id' => 'Default'
        ];

        $this->_data['configurable_product'] = [
            'product' =>
                [
                    'name' => 'configurable_product%isolation%',
                    'sku' => 'configurable_product%isolation%',
                    'price' => '50',
                    'tax_class_id' => 'Taxable Goods',
                    'weight' => '1',
                    'status' => 1,
                    'configurable_attributes_data' =>
                        [
                            'configurable_attribut_for_variations' =>
                                [
                                    'code' => 'attribute_for_variation_of_con',
                                    'attribute_id' => '%attributes_id%',
                                    'label' => 'attribute_for_variation_of_configurable_product',
                                    'values' =>
                                        [
                                            12 =>
                                                [
                                                    'value_index' => '12',
                                                    'pricing_value' => '2',
                                                    'is_percent' => 'No',
                                                    'include' => 'Yes',
                                                ],
                                            13 =>
                                                [
                                                    'value_index' => '13',
                                                    'pricing_value' => '3',
                                                    'is_percent' => 'No',
                                                    'include' => 'Yes'
                                                ],
                                        ],
                                ],
                        ],
                    'website_ids' =>
                        [
                            0 => 'Main Website',
                        ],
                    'stock_data' =>
                        [
                            'manage_stock' => 'Yes',
                            'use_config_enable_qty_increments' => 'Yes',
                            'use_config_qty_increments' => 'Yes',
                            'is_in_stock' => 'In Stock'
                        ],
                    'url_key' => 'configurable-product%isolation%',
                    'visibility' => 'Catalog, Search',
                ],
            'attributes' =>
                [
                    0 => '%attributes_id%',
                ],
            'variations-matrix' =>
                [
                    12 =>
                        [
                            'name' => 'configurable_product-firstVariation',
                            'configurable_attribute' => '{"attribute_for_variation_of_con":"12"}',
                            'sku' => 'configurable_product%isolation%-firstVariation',
                            'quantity_and_stock_status' =>
                                [
                                    'qty' => '111',
                                ],
                            'weight' => '1',
                        ],
                    13 =>
                        [
                            'name' => 'configurable_product-secondVariation',
                            'configurable_attribute' => '{"attribute_for_variation_of_con":"13"}',
                            'sku' => 'configurable_product%isolation%-secondVariation',
                            'quantity_and_stock_status' =>
                                [
                                    'qty' => '222',
                                ],
                            'weight' => '1',
                        ],
                ],
            'affect_configurable_product_attributes' => '1',
            'new-variations-attribute-set-id' => 'Default',
        ];

    }
}
