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
                    'path' => \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_ALLOW,
                    'scope' => 'currency',
                    'scope_id' => '1',
                    'value' => ['USD', 'UAH'],
                ],
            ]
        ];

        $this->_data['config_currency_symbols_usd_and_chf'] = [
            'section' => [
                [
                    'path' => 'currency/options/allow',
                    'scope' => 'currency',
                    'scope_id' => '1',
                    'value' => ['USD', 'CHF'],
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

        $this->_data['disable_shipping_all'] = [
            'section' => [
                [
                    'path' => 'carriers/flatrate/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/freeshipping/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/ups/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/usps/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/fedex/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/dhl/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ]
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

        $this->_data['ups'] = [
            'section' => [
                [
                    'path' => 'carriers/ups/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
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
                    'value' => 'UPS_XML',
                ],
                [
                    'path' => 'carriers/ups/is_account_live',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/ups/password',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'magento200',
                ],
                [
                    'path' => 'carriers/ups/username',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'magento',
                ],
                [
                    'path' => 'carriers/ups/mode_xml',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/ups/gateway_url',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'https://wwwcie.ups.com/ups.app/xml/Rate',
                ],
                [
                    'path' => 'carriers/ups/origin_shipment',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'Shipments Originating in United States',
                ],
                [
                    'path' => 'carriers/ups/access_license_number',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'ECAB751ABF189ECA',
                ],
                [
                    'path' => 'carriers/ups/negotiated_active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/ups/shipper_number',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => '207W88',
                ],
                [
                    'path' => 'carriers/ups/container',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'CP',
                ],
                [
                    'path' => 'carriers/ups/dest_type',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'RES',
                ],
                [
                    'path' => 'carriers/ups/tracking_xml_url',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'https://wwwcie.ups.com/ups.app/xml/Track',
                ],
                [
                    'path' => 'carriers/ups/unit_of_measure',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'LBS',
                ],
                [
                    'path' => 'carriers/ups/allowed_methods',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => ['11', '12', '14', '54', '59', '65', '01', '02', '03', '07', '08'],
                ],
                [
                    'path' => 'carriers/ups/sallowspecific',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/ups/showmethod',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 0,
                ],
                [
                    'path' => 'carriers/ups/debug',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ],
            ]
        ];

        $this->_data['usps'] = [
            'section' => [
                [
                    'path' => 'carriers/usps/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
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
                    'value' => 'https://secure.shippingapis.com/ShippingAPI.dll',
                ],
                [
                    'path' => 'carriers/usps/userid',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => '721FRAGR6267',
                ],
                [
                    'path' => 'carriers/usps/password',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => '326ZL84XF990',
                ],
            ]
        ];

        $this->_data['dhl_eu'] = [
            'section' => [
                [
                    'path' => 'carriers/dhl/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'carriers/dhl/gateway_url',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'https://xmlpitest-ea.dhl.com/XMLShippingServlet',
                ],
                [
                    'path' => 'carriers/dhl/id',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'EvgeniyDE',
                ],
                [
                    'path' => 'carriers/dhl/password',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'aplNb6Rop',
                ],
                [
                    'path' => 'carriers/dhl/account',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => '152691811',
                ],
                [
                    'path' => 'carriers/dhl/showmethod',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'carriers/dhl/debug',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ],
            ]
        ];

        $this->_data['fedex'] = [
            'section' => [
                [
                    'path' => 'carriers/fedex/active',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'carriers/fedex/account',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => '510087801',
                ],
                [
                    'path' => 'carriers/fedex/meter_number',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => '100047915',
                ],
                [
                    'path' => 'carriers/fedex/key',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'INdxa6ug7qZ2KD7y',
                ],
                [
                    'path' => 'carriers/fedex/password',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'pTfh4K0nkHcHVginelU4HmJkA',
                ],
                [
                    'path' => 'carriers/fedex/sandbox_mode',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 1,
                ],
                [
                    'path' => 'shipping/origin/country_id',
                    'scope' => 'carriers',
                    'scope_id' => 1,
                    'value' => 'US',
                ],
                [
                    'path' => 'shipping/origin/region_id',
                    'scope' => 'shipping',
                    'scope_id' => 1,
                    'value' => '12',
                ],
                [
                    'path' => 'shipping/origin/postcode',
                    'scope' => 'shipping',
                    'scope_id' => 1,
                    'value' => '90024',
                ],
                [
                    'path' => 'shipping/origin/city',
                    'scope' => 'shipping',
                    'scope_id' => 1,
                    'value' => 'Los Angeles',
                ],
                [
                    'path' => 'shipping/origin/street_line1',
                    'scope' => 'shipping',
                    'scope_id' => 1,
                    'value' => '1419 Westwood Blvd',
                ],
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

        $this->_data['salesarchive_complete'] = [
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
                    'value' => ['complete'],
                ],
            ]
        ];

        $this->_data['salesarchive_all_statuses'] = [
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
                    'value' => ['pending', 'processed_ogone', 'processing', 'complete', 'closed', 'canceled', 'holded'],
                ],
            ]
        ];

        $this->_data['upsell_position_limit_default'] = [
            'section' => [
                [
                    'path' => 'catalog/magento_targetrule/upsell_position_limit',
                    'scope' => 'catalog',
                    'scope_id' => 1,
                    'value' => 8,
                ],
            ]
        ];

        $this->_data['upsell_position_limit_one'] = [
            'section' => [
                [
                    'path' => 'catalog/magento_targetrule/upsell_position_limit',
                    'scope' => 'catalog',
                    'scope_id' => 1,
                    'value' => 1,
                ],
            ]
        ];
    }
}
