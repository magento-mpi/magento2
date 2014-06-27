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
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['50_dollar_product'] = [
            'sku' => 'virtual_50_dollar_product%isolation%',
            'name' => 'virtual_50_dollar_product%isolation%',
            'attribute_set_id' => ['dataSet' => 'default'],
            'price' => ['value' => 50, 'preset' => '-'],
        ];
    }
}
