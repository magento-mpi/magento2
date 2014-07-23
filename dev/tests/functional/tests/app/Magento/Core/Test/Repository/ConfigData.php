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
     * @SuppressWarnings
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

        $this->_data['reward_points_with_regestration_reward'] = [
            'section' => [
                [
                    'path' => 'magento_reward/points/order',
                    'scope' => 'magento_reward',
                    'scope_id' => '1',
                    'value' => '1',
                ],
                [
                    'path' => 'magento_reward/points/register',
                    'scope' => 'magento_reward',
                    'scope_id' => '1',
                    'value' => '10',
                ],
            ],
        ];

        $this->_data['reward_purchase'] = [
            'section' => [
                [
                    'path' => 'magento_reward/points/order',
                    'scope' => 'magento_reward',
                    'scope_id' => '1',
                    'value' => '1',
                ],
            ],
        ];

        $this->_data['reward_points_with_regestration_reward_rollback'] = [
            'section' => [
                [
                    'path' => 'magento_reward/points/order',
                    'scope' => 'magento_reward',
                    'scope_id' => '1',
                    'value' => '0',
                ],
                [
                    'path' => 'magento_reward/points/register',
                    'scope' => 'magento_reward',
                    'scope_id' => '1',
                    'value' => '',
                ],
            ],
        ];

        $this->_data['reward_purchase_rollback'] = [
            'section' => [
                [
                    'path' => 'magento_reward/points/order',
                    'scope' => 'magento_reward',
                    'scope_id' => '1',
                    'value' => '0',
                ],
            ],
        ];
    }
}
