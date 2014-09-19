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

        //Store Information
        $this->_data['store_information'] = [
            'section' => [
                [
                    'path' => 'general/store_information/name',
                    'scope' => 'general',
                    'scope_id' => 1,
                    'value' => 'Store 1',
                ],
                [
                    'path' => 'general/store_information/phone',
                    'scope' => 'general',
                    'scope_id' => 1,
                    'value' => '1234-123-123',
                ],
                [
                    'path' => 'general/store_information/country_id',
                    'scope' => 'general',
                    'scope_id' => 1,
                    'value' => 'US',
                ],
                [
                    'path' => 'general/store_information/region_id',
                    'scope' => 'general',
                    'scope_id' => 1,
                    'value' => 12,
                ],
                [
                    'path' => 'general/store_information/postcode',
                    'scope' => 'general',
                    'scope_id' => 1,
                    'value' => 90322,
                ],
                [
                    'path' => 'general/store_information/city',
                    'scope' => 'general',
                    'scope_id' => 1,
                    'value' => 'Culver City',
                ],
                [
                    'path' => 'general/store_information/street_line1',
                    'scope' => 'general',
                    'scope_id' => 1,
                    'value' => '10441 Jefferson Blvd',
                ],
                [
                    'path' => 'general/store_information/street_line2',
                    'scope' => 'general',
                    'scope_id' => 1,
                    'value' => 'Suite 200',
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

        // Shipping origin settings
        $this->_data['shipping_origin'] = [
            'section' => [
                [
                    'path' => 'shipping/origin/country_id',
                    'scope' => 'shipping',
                    'scope_id' => 1,
                    'value' => 'US',
                ],
                [
                    'path' => 'shipping/origin/region_id',
                    'scope' => 'shipping',
                    'scope_id' => 1,
                    'value' => 12,
                ],
                [
                    'path' => 'shipping/origin/postcode',
                    'scope' => 'shipping',
                    'scope_id' => 1,
                    'value' => 90232,
                ],
                [
                    'path' => 'shipping/origin/city',
                    'scope' => 'shipping',
                    'scope_id' => 1,
                    'value' => 'Culver City',
                ],
                [
                    'path' => 'shipping/origin/street_line1',
                    'scope' => 'shipping',
                    'scope_id' => 1,
                    'value' => '10441 Jefferson Blvd',
                ],
                [
                    'path' => 'shipping/origin/street_line2',
                    'scope' => 'shipping',
                    'scope_id' => 1,
                    'value' => 'Suite 200',
                ],
            ]
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

        $this->_data['freeshipping_disabled'] = [
            'section' => [
                [
                    'path' => 'carriers/freeshipping/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/freeshipping/name',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'Free',
                ],
                [
                    'path' => 'carriers/freeshipping/title',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'Free Shipping',
                ],
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
                ],
                [
                    'path' => 'carriers/flatrate/title',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'Flat Rate',
                ],
                [
                    'path' => 'carriers/flatrate/name',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'Fixed',
                ],
                [
                    'path' => 'carriers/flatrate/type',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'I',
                ],
                [
                    'path' => 'carriers/flatrate/price',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 5.0,
                ],
                [
                    'path' => 'carriers/flatrate/handling_type',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'F',
                ],
                [
                    'path' => 'carriers/flatrate/specificerrmsg',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'This shipping method is not available. To use this shipping method, please contact us.',
                ],
            ]
        ];

        $this->_data['ups_disable'] = [
            'section' => [
                [
                    'path' => 'carriers/ups/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/ups/active_rma',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'carriers/ups/type',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'UPS',
                ],
                [
                    'path' => 'carriers/ups/gateway_url',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'http://www.ups.com/using/services/rave/qcostcgi.cgi',
                ],
                [
                    'path' => 'carriers/ups/title',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'United Parcel Service',
                ],
                [
                    'path' => 'carriers/ups/max_package_weight',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 150,
                ],
                [
                    'path' => 'carriers/ups/min_package_weight',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0.1,
                ],
                [
                    'path' => 'carriers/ups/handling_type',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'F',
                ],
                [
                    'path' => 'carriers/ups/handling_action',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'O',
                ],
                [
                    'path' => 'carriers/ups/allowed_methods',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    // @codingStandardsIgnoreStart
                    'value' => '1DM,1DML,1DA,1DAL,1DAPI,1DP,1DPL,2DM,2DML,2DA,2DAL,3DS,GND,GNDCOM,GNDRES,STD,XPR,WXS,XPRL,XDM,XDML,XPD',
                    // @codingStandardsIgnoreEnd
                ],
                [
                    'path' => 'carriers/ups/free_method',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'GND',
                ],
            ]
        ];

        $this->_data['usps_disable'] = [
            'section' => [
                [
                    'path' => 'carriers/usps/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/usps/active_rma',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'carriers/usps/type',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'UPS',
                ],
                [
                    'path' => 'carriers/usps/gateway_url',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'http://production.shippingapis.com/ShippingAPI.dll',
                ],
                [
                    'path' => 'carriers/usps/gateway_secure_url',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'http://production.shippingapis.com/ShippingAPI.dll',
                ],
                [
                    'path' => 'carriers/usps/title',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'United States Postal Service',
                ],
                [
                    'path' => 'carriers/usps/max_package_weight',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 70,
                ],
                [
                    'path' => 'carriers/usps/min_package_weight',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0.1,
                ],
                [
                    'path' => 'carriers/usps/handling_type',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'F',
                ],
                [
                    'path' => 'carriers/usps/handling_action',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'O',
                ],
                [
                    'path' => 'carriers/usps/allowed_methods',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    // @codingStandardsIgnoreStart
                    'value' => '0_FCLE,0_FCL,0_FCP,1,2,3,4,6,7,13,16,17,22,23,25,27,28,33,34,35,36,37,42,43,53,55,56,57,61,INT_1,INT_2,INT_4,INT_6,INT_7,INT_8,INT_9,INT_10,INT_11,INT_12,INT_13,INT_14,INT_15,INT_16,INT_20,INT_26',
                    // @codingStandardsIgnoreEnd
                ],
            ]
        ];

        $this->_data['fedex_disable'] = [
            'section' => [
                [
                    'path' => 'carriers/fedex/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/fedex/active_rma',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/fedex/production_webservices_url',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'https://ws.fedex.com:443/web-services/',
                ],
                [
                    'path' => 'carriers/fedex/title',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'Federal Express',
                ],
                [
                    'path' => 'carriers/fedex/max_package_weight',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 150,
                ],
                [
                    'path' => 'carriers/fedex/handling_type',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'F',
                ],
                [
                    'path' => 'carriers/fedex/handling_action',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'O',
                ],
                [
                    'path' => 'carriers/fedex/allowed_methods',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    // @codingStandardsIgnoreStart
                    'value' => 'EUROPE_FIRST_INTERNATIONAL_PRIORITY,FEDEX_1_DAY_FREIGHT,FEDEX_2_DAY_FREIGHT,FEDEX_2_DAY,FEDEX_2_DAY_AM,FEDEX_3_DAY_FREIGHT,FEDEX_EXPRESS_SAVER,FEDEX_GROUND,FIRST_OVERNIGHT,GROUND_HOME_DELIVERY,INTERNATIONAL_ECONOMY,INTERNATIONAL_ECONOMY_FREIGHT,INTERNATIONAL_FIRST,INTERNATIONAL_GROUND,INTERNATIONAL_PRIORITY,INTERNATIONAL_PRIORITY_FREIGHT,PRIORITY_OVERNIGHT,SMART_POST,STANDARD_OVERNIGHT,FEDEX_FREIGHT,FEDEX_NATIONAL_FREIGHT',
                    // @codingStandardsIgnoreEnd
                ],
                [
                    'path' => 'carriers/fedex/free_method',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'FEDEX_GROUND',
                ],
            ]
        ];

        $this->_data['dhl_disable'] = [
            'section' => [
                [
                    'path' => 'carriers/dhl/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
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

        //Sales Archiving
        $this->_data['salesarchive_pending_closed'] = [
            'section' => [
                [
                    'path' => 'sales/magento_salesarchive/active',
                    'scope' => 'sales',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'sales/magento_salesarchive/order_statuses',
                    'scope' => 'sales',
                    'scope_id' => 1,
                    'value' => ['pending', 'closed'],
                ],
            ]
        ];
    }
}
