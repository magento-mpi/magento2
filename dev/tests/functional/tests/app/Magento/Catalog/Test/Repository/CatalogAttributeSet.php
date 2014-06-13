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
class CatalogAttributeSet extends AbstractRepository
{
    /**
     * Construct
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'attribute_set_name' => 'Default%isolation%',
            'attribute_set_id' => 4,
        ];
    }
}
