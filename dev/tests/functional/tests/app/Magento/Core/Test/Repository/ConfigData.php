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
     * @constructor
     * @param array $defaultConfig [optional]
     * @param array $defaultData [optional]
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['compare_products'] = [
            'section' => [
                [
                    'path' => 'catalog/recently_products/scope',
                    'scope' => 'catalog',
                    'scope_id' => 1,
                    'value' => 'Website',
                ],
                [
                    'path' => 'catalog/recently_products/viewed_count',
                    'scope' => 'catalog',
                    'scope_id' => 1,
                    'value' => 5,
                ],
                [
                    'path' => 'catalog/recently_products/compared_count',
                    'scope' => 'catalog',
                    'scope_id' => 1,
                    'value' => 12,
                ],
            ]
        ];

        $this->_data['reward_points_with_registration_reward'] = [
            'section' => [
                [
                    'path' => 'magento_reward/points/order',
                    'scope' => 'magento_reward',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'magento_reward/points/register',
                    'scope' => 'magento_reward',
                    'scope_id' => 1,
                    'value' => 10,
                ],
            ],
        ];

        $this->_data['reward_purchase'] = [
            'section' => [
                [
                    'path' => 'magento_reward/points/order',
                    'scope' => 'magento_reward',
                    'scope_id' => 1,
                    'value' => 1,
                ],
            ],
        ];

        $this->_data['reward_points_with_registration_reward_rollback'] = [
            'section' => [
                [
                    'path' => 'magento_reward/points/order',
                    'scope' => 'magento_reward',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'magento_reward/points/register',
                    'scope' => 'magento_reward',
                    'scope_id' => 1,
                    'value' => '',
                ],
            ],
        ];

        $this->_data['reward_purchase_rollback'] = [
            'section' => [
                [
                    'path' => 'magento_reward/points/order',
                    'scope' => 'magento_reward',
                    'scope_id' => 1,
                    'value' => 0,
                ],
            ],
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

        $this->_data['multiple_wishlist_default'] = [
            'section' => [
                [
                    'path' => 'wishlist/general/multiple_enabled',
                    'scope' => 'wishlist',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'wishlist/general/multiple_wishlist_number',
                    'scope' => 'wishlist',
                    'scope_id' => 1,
                    'value' => 3,
                ],
            ]
        ];

        $this->_data['disabled_multiple_wishlist_default'] = [
            'section' => [
                [
                    'path' => 'wishlist/general/multiple_enabled',
                    'scope' => 'wishlist',
                    'scope_id' => 1,
                    'value' => 0,
                ],
            ]
        ];
    }
}
