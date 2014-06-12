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
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'name' => 'Test grouped product %isolation%',
            'sku' => 'sku_test_grouped_product_%isolation%',
            'price' => ['value' => 120.00],
            'weight' => 30.0000,
            'grouped_products' => ['products' => 'catalogProductSimple::simple_for_composite_products'],
            'status' => 'Product online',
            'visibility' => 'Catalog, Search',
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'url_key' => 'test-grouped-product-%isolation%',
            'quantity_and_stock_status' => 'In Stock',
            'website_ids' => ['Main Website'],
            'attribute_set_id' => 'Default',
        ];
    }
}
