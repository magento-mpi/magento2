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
 * Class StoreGroup
 * Data for creation Store Group
 */
class StoreGroup extends AbstractRepository
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
            'website_id' => [
                'dataSet' => 'main_website'
            ],
            'name' => 'StoreGroup%isolation%',
            'root_category_id' => [
                'dataSet' => 'default_category'
            ],
        ];

        $this->_data['custom'] = [
            'website_id' => [
                'dataSet' => 'main_website'
            ],
            'name' => 'Custom Store',
            'root_category_id' => [
                'dataSet' => 'default_category'
            ],
        ];
    }
}
