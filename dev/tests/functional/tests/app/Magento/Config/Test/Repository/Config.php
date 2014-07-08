<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Config\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Config
 * Data for creation Config settings
 */
class Config extends AbstractRepository
{
    /**
     * Constructor
     *
     * @param array $defaultConfig [optional]
     * @param array $defaultData [optional]
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['compare_products'] = [
            'section' => [
                'catalog/recently_products/scope' => 'Website',
                'catalog/recently_products/viewed_count' => '5',
                'catalog/recently_products/compared_count' => '12'
            ]
        ];
    }
}
