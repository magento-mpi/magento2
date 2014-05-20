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
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['SimpleProduct_sku_516169631'] = [
            'sku' => 'SimpleProduct_sku_516169631',
            'name' => 'SimpleProduct 516169631',
            'type_id' => 'simple',
            'attribute_set_id' => '4',
            'price' => ['value' => 3, 'preset' => '-'],
            'id' => '1',
            'mtf_dataset_name' => 'SimpleProduct_sku_516169631'
        ];

        $this->_data['SimpleProduct_sku_1947585255'] = [
            'sku' => 'SimpleProduct_sku_1947585255',
            'name' => 'SimpleProduct 1947585255',
            'type_id' => 'simple',
            'attribute_set_id' => '4',
            'price' => ['value' => 4, 'preset' => '-'],
            'id' => '2',
            'mtf_dataset_name' => 'SimpleProduct_sku_1947585255'
        ];

        $this->_data['100_dollar_product'] = [
            'sku' => '100_dollar_product%isolation%',
            'name' => '100_dollar_product%isolation%',
            'type_id' => 'simple',
            'attribute_set_id' => '4',
            'price' => ['value' => 100, 'preset' => '-'],
        ];

        $this->_data['40_dollar_product'] = [
            'sku' => '40_dollar_product',
            'name' => '40_dollar_product',
            'type_id' => 'simple',
            'attribute_set_id' => '4',
            'price' => ['value' => 40, 'preset' => '-'],
            'id' => '2',
            'mtf_dataset_name' => '40_dollar_product'
        ];

        $this->_data['MAGETWO-23036'] = [
            'sku' => 'MAGETWO-23036',
            'name' => 'simple_with_category',
            'type_id' => 'simple',
            'attribute_set_id' => '4',
            'price' => ['value' => 100, 'preset' => 'MAGETWO-23036'],
            'id' => '3',
            'category_ids' => ['presets' => 'default'],
            'mtf_dataset_name' => 'simple_with_category'
        ];

        $this->_data['product_with_category'] = [
            'sku' => 'simple_product_with_category_%isolation%',
            'name' => 'Simple product with category %isolation%',
            'type_id' => 'simple',
            'attribute_set_id' => '4',
            'price' => ['value' => 100, 'preset' => ''],
            'category_ids' => ['presets' => 'default_subcategory'],
            'mtf_dataset_name' => 'simple_with_category',
        ];

        $this->_data['simple_for_salesrule_1'] = [
            'type_id' => 'simple',
            'attribute_set_id' => '4',
            'name' => 'Simple Product %isolation%',
            'sku' => 'sku_simple_product_%isolation%',
            'price' => ['value' => 100, 'preset' => ''],
            'weight' => 100,
            'category_ids' => ['presets' => 'default_subcategory']
        ];

        $this->_data['simple_for_salesrule_2'] = [
            'type_id' => 'simple',
            'attribute_set_id' => '4',
            'name' => 'Simple Product %isolation%',
            'sku' => 'sku_simple_product_%isolation%',
            'price' => ['value' => 50, 'preset' => ''],
            'weight' => 50,
            'category_ids' => ['presets' => 'default_subcategory']
        ];
    }
}
