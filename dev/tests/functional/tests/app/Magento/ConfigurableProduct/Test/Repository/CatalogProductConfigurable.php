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
    /**
     * Constructor
     *
     * @param array $defaultConfig [optional]
     * @param array $defaultData [optional]
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'name' => 'Test configurable product %isolation%',
            'sku' => 'sku_test_configurable_product_%isolation%',
            'price' => ['value' => 120.00],
            'weight' => 30.0000,
            'status' => 'Product online',
            'visibility' => 'Catalog, Search',
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'url_key' => 'test-configurable-product-%isolation%',
            'configurable_attributes_data' => ['preset' => 'default'],
            'quantity_and_stock_status' => [
                'is_in_stock' => 'In Stock',
            ],
            'stock_data' => [
                'manage_stock' => 'Yes',
                'is_in_stock' => 'In Stock',
            ],
            'website_ids' => ['Main Website'],
            'attribute_set_id' => 'Default',
        ];
    }
}
