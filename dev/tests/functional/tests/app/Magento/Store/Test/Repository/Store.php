<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Store
 * Data for creation Catalog Price Rule
 */
class Store extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'group_id' => 'Main Website Store',
            'name' => 'Custom_Store_%isolation%',
            'code' => 'code_%isolation%',
            'is_active' => 'Enabled',
            'store_id' => 1,
        ];

        $this->_data['All Store Views'] = [
            'name' => 'All Store Views',
            'store_id' => 0,
        ];

        $this->_data['german'] = [
            'group_id' => 'Main Website Store',
            'name' => 'DE%isolation%',
            'code' => 'de%isolation%',
            'is_active' => 'Enabled',
        ];
    }
}
