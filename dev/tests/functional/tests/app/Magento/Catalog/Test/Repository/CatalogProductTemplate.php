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
 * Class CatalogProductTemplate
 * Data for creation Product Template
 */
class CatalogProductTemplate extends AbstractRepository
{
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'attribute_set_name' => 'product_template_default_%isolation%',
            'skeleton_set' => 'Default'
        ];
    }
}
