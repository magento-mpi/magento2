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
 * Class CatalogProductSimple
 * Data for creation Catalog Product Configurable
 */
class CatalogProductConfigurable extends AbstractRepository
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
            'name' => 'Configurable Product %isolation%',
            'sku' => 'sku_configurable_product_%isolation%',
            'price' => ['value' => 100.00],
            'weight' => 1
        ];

        $this->_data['customDefault'] = [
            'name' => 'Test configurable product %isolation%',
            'sku' => 'sku_test_configurable_product_%isolation%',
            'price' => ['value' => 120.00],
            'weight' => 30.0000,
            'status' => 'Product online',
            'visibility' => 'Catalog, Search',
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'url_key' => 'test-configurable-product-%isolation%',
            'configurable_options' => ['preset' => 'default'],
            'attribute_options' => ['preset' => 'default'],
            'configurable_attributes_data' => ['preset' => 'default'],
            'variations_matrix' => ['preset' => 'default'],
            'quantity_and_stock_status' => 'In Stock',
            'website_ids' => ['Main Website'],
            'attribute_set_id' => 'Default',
        ];
    }
}
