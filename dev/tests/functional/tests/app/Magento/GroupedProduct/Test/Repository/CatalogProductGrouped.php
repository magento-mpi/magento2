<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogProductGrouped
 * Data for creation Catalog Product Grouped
 */
class CatalogProductGrouped extends AbstractRepository
{
    /**
     * Constructor
     *
     * @param array $defaultConfig [optional]
     * @param array $defaultData [optional]
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'name' => 'Test grouped product %isolation%',
            'sku' => 'sku_test_grouped_product_%isolation%',
            'category_ids' => ['presets' => 'default'],
            'associated' => ['preset' => 'defaultSimpleProduct'],
            'status' => 'Product online',
            'visibility' => 'Catalog, Search',
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'url_key' => 'test-grouped-product-%isolation%',
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'stock_data' => [
                'use_config_manage_stock' => 'Yes',
                'manage_stock' => 'No',
            ],
            'website_ids' => ['Main Website'],
            'attribute_set_id' => ['dataSet' => 'default'],
        ];

        $this->_data['grouped_product_out_of_stock'] = [
            'name' => 'Test grouped product %isolation%',
            'sku' => 'sku_test_grouped_product_%isolation%',
            'category_ids' => ['presets' => 'default'],
            'associated' => ['preset' => 'defaultSimpleProduct'],
            'status' => 'Product online',
            'visibility' => 'Catalog, Search',
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'url_key' => 'test-grouped-product-%isolation%',
            'quantity_and_stock_status' => [
                'is_in_stock' => 'Out of Stock',
            ],
            'website_ids' => ['Main Website'],
            'attribute_set_id' => ['dataSet' => 'default'],
        ];

        $this->_data['grouped_product_with_price'] = [
            'name' => 'Test grouped product %isolation%',
            'sku' => 'sku_test_grouped_product_%isolation%',
            'price' => ['value' => '-', 'preset' => 'starting-560'],
            'category_ids' => ['presets' => 'default'],
            'associated' => ['preset' => 'defaultSimpleProduct'],
            'status' => 'Product online',
            'visibility' => 'Catalog, Search',
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'url_key' => 'test-grouped-product-%isolation%',
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'website_ids' => ['Main Website'],
            'attribute_set_id' => ['dataSet' => 'default'],
        ];
    }
}
