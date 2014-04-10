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
 *
 * @package Magento\Catalog\Test\Repository
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
            'sku' => '100_dollar_product',
            'name' => '100_dollar_product',
            'type_id' => 'simple',
            'attribute_set_id' => '4',
            'price' => ['value' => 100, 'preset' => '-'],
            'id' => '2',
            'mtf_dataset_name' => '100_dollar_product'
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
            'mtf_dataset_name' => 'simple_with_category',
        ];
    }
}
