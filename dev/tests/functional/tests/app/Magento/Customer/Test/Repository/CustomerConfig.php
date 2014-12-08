<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Repository;

use Magento\Core\Test\Repository;

/**
 * Class Customer Config Repository
 *
 */
class CustomerConfig extends Repository\Config
{
    /**
     * Config repository constructor
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        parent::__construct($defaultConfig, $defaultData);
        $this->_data['customer_vat'] = $this->getCustomerVat();
    }

    /**
     * General store and country options settings
     *
     * @return array
     */
    public function getCustomerVat()
    {
        return [
            'data' => [
                'sections' => [
                    'customer' => [
                        'section' => 'customer',
                        'website' => null,
                        'store' => null,
                        'groups' => [
                            'create_account' => [
                                'fields' => [
                                    'auto_group_assign' => [
                                        'value' => self::YES_VALUE,
                                    ],
                                    'tax_calculation_address_type' => [
                                        'value' => 'billing',
                                    ],
                                    'viv_domestic_group' => [
                                        'value' => '%valid_vat_id_domestic%',
                                    ],
                                    'viv_intra_union_group' => [
                                        'value' => '%valid_vat_id_union%',
                                    ],
                                    'viv_invalid_group' => [
                                        'value' => '%invalid_vat_id%',
                                    ],
                                    'viv_error_group' => [
                                        'value' => '%validation_error%',
                                    ],
                                    'vat_frontend_visibility' => [
                                        'value' => self::YES_VALUE,
                                    ],
                                ],
                            ],
                            'country' => [
                                'fields' => [
                                    'eu_countries' => [
                                        'value' => ['FR', 'DE', 'GB'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
