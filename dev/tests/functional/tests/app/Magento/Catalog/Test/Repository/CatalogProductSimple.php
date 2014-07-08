<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CatalogProductSimple
 * Data for creation Catalog Product Simple
 */
class CatalogProductSimple extends AbstractRepository
{
    /**
     * Constructor
     *
     * @param array $defaultConfig [optional]
     * @param array $defaultData [optional]
     * 
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'attribute_set_id' => ['dataSet' => 'default'],
            'name' => 'Simple Product %isolation%',
            'sku' => 'sku_simple_product_%isolation%',
            'category_ids' => ['presets' => 'default'],
            'weight' => 1,
            'quantity_and_stock_status' => [
                'qty' => 25.0000,
                'is_in_stock' => 'In Stock',
            ],
            'price' => ['value' => 560.00, 'preset' => '-'],
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'website_ids' => ['Main Website'],
        ];

        $this->_data['100_dollar_product'] = [
            'sku' => '100_dollar_product%isolation%',
            'name' => '100_dollar_product%isolation%',
            'type_id' => 'simple',
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'attribute_set_id' => ['dataSet' => 'default'],
            'price' => ['value' => 100, 'preset' => '-'],
            'website_ids' => ['Main Website'],
        ];

        $this->_data['40_dollar_product'] = [
            'sku' => '40_dollar_product%isolation%',
            'name' => '40_dollar_produc%isolation%',
            'type_id' => 'simple',
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'attribute_set_id' => ['dataSet' => 'default'],
            'price' => ['value' => 40, 'preset' => '-'],
            'mtf_dataset_name' => '40_dollar_product',
            'website_ids' => ['Main Website'],
        ];

        $this->_data['MAGETWO-23036'] = [
            'sku' => 'MAGETWO-23036',
            'name' => 'simple_with_category',
            'attribute_set_id' => ['dataSet' => 'default'],
            'type_id' => 'simple',
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'price' => ['value' => 100, 'preset' => 'MAGETWO-23036'],
            'category_ids' => ['presets' => 'default'],
            'mtf_dataset_name' => 'simple_with_category',
            'website_ids' => ['Main Website'],
        ];

        $this->_data['product_with_category'] = [
            'sku' => 'simple_product_with_category_%isolation%',
            'name' => 'Simple product with category %isolation%',
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'attribute_set_id' => ['dataSet' => 'default'],
            'price' => ['value' => 100, 'preset' => ''],
            'category_ids' => ['presets' => 'default_subcategory'],
            'website_ids' => ['Main Website'],
            'mtf_dataset_name' => 'simple_with_category',
        ];

        $this->_data['simple_for_salesrule_1'] = [
            'attribute_set_id' => ['dataSet' => 'default'],
            'type_id' => 'simple',
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'name' => 'Simple Product %isolation%',
            'sku' => 'sku_simple_product_%isolation%',
            'price' => ['value' => 100, 'preset' => ''],
            'weight' => 100,
            'website_ids' => ['Main Website'],
            'category_ids' => ['presets' => 'default_subcategory']
        ];

        $this->_data['simple_for_salesrule_2'] = [
            'attribute_set_id' => ['dataSet' => 'default'],
            'name' => 'Simple Product %isolation%',
            'sku' => 'sku_simple_product_%isolation%',
            'price' => ['value' => 50, 'preset' => ''],
            'weight' => 50,
            'website_ids' => ['Main Website'],
            'category_ids' => ['presets' => 'default_subcategory']
        ];

        $this->_data['product_with_special_price_and_category'] = [
            'sku' => 'simple_product_with_special_price_and_category%isolation%',
            'name' => 'Simple product with special price and category %isolation%',
            'attribute_set_id' => ['dataSet' => 'default'],
            'price' => ['value' => 100, 'preset' => ''],
            'special_price' => 90,
            'category_ids' => ['presets' => 'default_subcategory'],
            'website_ids' => ['Main Website'],
        ];

        $this->_data['adc_123_simple_for_advancedsearch'] = [
            'name' => 'adc_123',
            'sku' => 'adc_123',
            'price' => ['value' => 100.00, 'preset' => '-'],
            'tax_class_id' => ['dataSet' => 'None'],
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'Yes',
            ],
            'weight' => 1.0000,
            'description' => '<p>dfj_full</p>',
            'status' => 'Product online',
            'website_ids' => [
                0 => 'Main Website',
            ],
            'stock_data' => [
                'manage_stock' => 'Yes',
                'original_inventory_qty' => 666.0000,
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'visibility' => 'Catalog, Search',
        ];

        $this->_data['abc_dfj_simple_for_advancedsearch'] = [
            'name' => 'abc_dfj',
            'sku' => 'abc_dfj',
            'price' => ['value' => 50.00, 'preset' => '-'],
            'tax_class_id' => ['dataSet' => 'Taxable Goods'],
            'quantity_and_stock_status' => [
                'qty' => 666.0000,
                'is_in_stock' => 'In Stock',
            ],
            'weight' => 1.0000,
            'description' => '<p>adc_Full</p>',
            'status' => 'Product online',
            'short_description' => '<p>abc_short</p>',
            'website_ids' => [
                0 => 'Main Website',
            ],
            'stock_data' => [
                'manage_stock' => 'Yes',
                'original_inventory_qty' => 666.0000,
                'qty' => 666.0000,
                'is_in_stock' => 'Yes',
            ],
            'visibility' => 'Catalog, Search',
        ];

        $this->_data['100_dollar_product_for_tax_rule'] = [
            'sku' => '100_dollar_product%isolation%',
            'name' => '100_dollar_product%isolation%',
            'attribute_set_id' => ['dataSet' => 'default'],
            'type_id' => 'simple',
            'quantity_and_stock_status' => [
                'qty' => 25.0000,
                'is_in_stock' => 'In Stock',
            ],
            'price' => ['value' => 100, 'preset' => '-'],
            'website_ids' => ['Main Website'],
        ];
    }
}
