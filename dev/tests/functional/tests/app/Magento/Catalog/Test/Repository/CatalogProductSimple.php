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
            'price' => 3,
            'id' => '1',
            'mtf_dataset_name' => 'SimpleProduct_sku_516169631',
        ];

        $this->_data['SimpleProduct_sku_1947585255'] = [
            'sku' => 'SimpleProduct_sku_1947585255',
            'name' => 'SimpleProduct 1947585255',
            'type_id' => 'simple',
            'attribute_set_id' => '4',
            'price' => 4,
            'id' => '2',
            'mtf_dataset_name' => 'SimpleProduct_sku_1947585255',
        ];
    }
}
