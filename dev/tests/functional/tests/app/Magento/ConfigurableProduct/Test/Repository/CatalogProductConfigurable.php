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
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'name' => 'Configurable Product %isolation%',
            'sku' => 'sku_configurable_product_%isolation%',
            'price' => ['value' => 100.00],
            'weight' => 1
        ];
    }
}
