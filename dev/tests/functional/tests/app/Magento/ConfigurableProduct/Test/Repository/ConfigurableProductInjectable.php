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
 * Class ConfigurableProductInjectable
 * Data for creation Catalog Product Configurable
 */
class ConfigurableProductInjectable extends AbstractRepository
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
            'name' => 'Test configurable product %isolation%',
            'sku' => 'sku_test_configurable_product_%isolation%',
            'price' => ['value' => 120.00],
            'weight' => 30.0000,
            'status' => 'Product online',
            'visibility' => 'Catalog, Search',
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'url_key' => 'configurable-product-%isolation%',
            'configurable_attributes_data' => ['preset' => 'default'],
            'quantity_and_stock_status' => [
                'is_in_stock' => 'In Stock',
            ],
            'website_ids' => ['Main Website'],
            'attribute_set_id' => ['dataSet' => 'default'],
            'checkout_data' => ['preset' => 'default'],
        ];

        $this->_data['one_variation'] = [
            'name' => 'Test configurable product %isolation%',
            'sku' => 'sku_test_configurable_product_%isolation%',
            'price' => ['value' => 120.00],
            'weight' => 30.0000,
            'status' => 'Product online',
            'visibility' => 'Catalog, Search',
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'url_key' => 'test-configurable-product-%isolation%',
            'configurable_attributes_data' => ['preset' => 'one_variation'],
            'quantity_and_stock_status' => [
                'is_in_stock' => 'In Stock',
            ],
            'website_ids' => ['Main Website'],
            'attribute_set_id' => ['dataSet' => 'default'],
        ];
    }
}
