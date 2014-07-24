<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class ConfigData
 * Data for creation Config settings
 */
class ConfigData extends AbstractRepository
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
                [
                    'path' => 'catalog/recently_products/scope',
                    'scope' => 'catalog',
                    'scope_id' => '1',
                    'value' => 'Website',
                ],
                [
                    'path' => 'catalog/recently_products/viewed_count',
                    'scope' => 'catalog',
                    'scope_id' => '1',
                    'value' => '5',
                ],
                [
                    'path' => 'catalog/recently_products/compared_count',
                    'scope' => 'catalog',
                    'scope_id' => '1',
                    'value' => '12',
                ],
            ]
        ];
        $this->_data['config_currency_symbols_usd_and_uah'] = [
            'section' => [
                [
                    'path' => 'currency/options/allow',
                    'scope' => 'currency',
                    'scope_id' => '1',
                    'value' => ['USD', 'UAH'],
                ],
            ]
        ];
        $this->_data['config_currency_symbols_usd'] = [
            'section' => [
                [
                    'path' => 'currency/options/allow',
                    'scope' => 'currency',
                    'scope_id' => '1',
                    'value' => ['USD'],
                ],
            ]
        ];
    }
}
