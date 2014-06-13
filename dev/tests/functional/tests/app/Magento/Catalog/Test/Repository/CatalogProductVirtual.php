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
 * Class CatalogVirtualProduct
 * Virtual product for precondition
 */
class CatalogProductVirtual extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['50_dollar_product'] = [
            'sku' => '50_dollar_product%isolation%',
            'name' => '50_dollar_product%isolation%',
            'type_id' => 'virtual',
            'attribute_set_id' => '4',
            'price' => ['value' => 50, 'preset' => '-'],
            'id' => '3',
            'mtf_dataset_name' => '50_dollar_product'
        ];
    }
}
