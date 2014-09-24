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

        $this->_data['config_currency_symbols_usd_and_uah'] = [
            'section' => [
                [
                    'path' => \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_ALLOW,
                    'scope' => 'currency',
                    'scope_id' => '1',
                    'value' => ['USD', 'UAH'],
                ],
            ]
        ];

        $this->_data['config_currency_symbols_usd'] = [
            'section' => [
                [
                    'path' => \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_ALLOW,
                    'scope' => 'currency',
                    'scope_id' => '1',
                    'value' => ['USD'],
                ],
            ]
        ];

        // Reward settings
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

        // Shipping settings
        $this->_data['freeshipping'] = [
            'section' => [
                [
                    'path' => 'carriers/freeshipping/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ]
            ]
        ];

        $this->_data['freeshipping_rollback'] = [
            'section' => [
                [
                    'path' => 'carriers/freeshipping/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ]
            ]
        ];

        $this->_data['freeshipping_specificcountry_gb'] = [
            'section' => [
                [
                    'path' => 'carriers/freeshipping/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'carriers/freeshipping/sallowspecific',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'carriers/freeshipping/specificcountry/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'GB',
                ],
            ]
        ];

        $this->_data['freeshipping_specificcountry_gb_rollback'] = [
            'section' => [
                [
                    'path' => 'carriers/freeshipping/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/freeshipping/sallowspecific',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
            ]
        ];

        $this->_data['flatrate'] = [
            'section' => [
                [
                    'path' => 'carriers/flatrate/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ]
            ]
        ];

        // Payments settings
        $this->_data['cashondelivery'] = [
            'section' =>
                [
                    [
                        'path' => 'payment/cashondelivery/active',
                        'scope' => 'payment',
                        'scope_id' => 1,
                        'value' => 1,
                    ]
                ]
        ];

        $this->_data['cashondelivery_rollback'] = [
            'section' => [
                [
                    'path' => 'payment/cashondelivery/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ]
            ]
        ];

        $this->_data['cashondelivery_specificcountry_gb'] = [
            'section' => [
                [
                    'path' => 'payment/cashondelivery/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'payment/cashondelivery/allowspecific',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'payment/cashondelivery/specificcountry/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 'GB',
                ],
            ]
        ];

        $this->_data['cashondelivery_specificcountry_gb_rollback'] = [
            'section' => [
                [
                    'path' => 'payment/cashondelivery/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'payment/cashondelivery/allowspecific',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ],
            ]
        ];

        $this->_data['checkmo'] = [
            'section' => [
                [
                    'path' => 'payment/checkmo/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ]
            ]
        ];

        $this->_data['checkmo_rollback'] = [
            'section' => [
                [
                    'path' => 'payment/checkmo/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ]
            ]
        ];

        $this->_data['checkmo_specificcountry_gb'] = [
            'section' => [
                [
                    'path' => 'payment/checkmo/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'payment/checkmo/allowspecific',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'payment/checkmo/specificcountry/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 'GB',
                ],
            ]
        ];

        $this->_data['checkmo_specificcountry_gb_rollback'] = [
            'section' => [
                [
                    'path' => 'payment/checkmo/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'payment/checkmo/allowspecific',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ],
            ]
        ];

        $this->_data['banktransfer'] = [
            'section' => [
                [
                    'path' => 'payment/banktransfer/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ]
            ]
        ];

        $this->_data['banktransfer_rollback'] = [
            'section' => [
                [
                    'path' => 'payment/banktransfer/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ]
            ]
        ];

        $this->_data['banktransfer_specificcountry_gb'] = [
            'section' => [
                [
                    'path' => 'payment/banktransfer/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'payment/banktransfer/allowspecific',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'payment/banktransfer/specificcountry/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 'GB',
                ],
            ]
        ];

        $this->_data['banktransfer_specificcountry_gb_rollback'] = [
            'section' => [
                [
                    'path' => 'payment/banktransfer/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'payment/banktransfer/allowspecific',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ],
            ]
        ];

        $this->_data['purchaseorder'] = [
            'section' => [
                [
                    'path' => 'payment/purchaseorder/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ]
            ]
        ];

        $this->_data['purchaseorder_rollback'] = [
            'section' => [
                [
                    'path' => 'payment/purchaseorder/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ]
            ]
        ];

        $this->_data['purchaseorder_specificcountry_gb'] = [
            'section' => [
                [
                    'path' => 'payment/purchaseorder/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'payment/purchaseorder/allowspecific',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'payment/purchaseorder/specificcountry/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 'GB',
                ],
            ]
        ];

        $this->_data['purchaseorder_specificcountry_gb_rollback'] = [
            'section' => [
                [
                    'path' => 'payment/purchaseorder/active',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'payment/purchaseorder/allowspecific',
                    'scope' => 'payment',
                    'scope_id' => 1,
                    'value' => 0,
                ],
            ]
        ];

        //Multiple wishlist
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
