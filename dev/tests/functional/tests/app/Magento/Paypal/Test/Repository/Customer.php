<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Paypal\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Customer Repository
 * Paypal buyer account
 *
 */
class Customer extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'config' => $defaultConfig,
            'data' => $defaultData,
        ];

        $this->_data['customer_US'] = $this->_getCustomerUS();
        $this->_data['address_US_1'] = $this->_getAddressUS1();
    }

    protected function _getCustomerUS()
    {
        return [
            'data' => [
                'fields' => [
                    'login_email' => [
                        'value' => 'mtf_personal@example.com',
                    ],
                    'login_password' => [
                        'value' => '12345678',
                    ],
                ],
            ]
        ];
    }

    protected function _getAddressUS1()
    {
        return [
            'data' => [
                'fields' => [
                    'firstname' => [
                        'value' => 'Dmytro',
                    ],
                    'lastname' => [
                        'value' => 'Aponasenko',
                    ],
                    'street' => [
                        'value' => '1 Main St',
                    ],
                    'city' => [
                        'value' => 'Culver City',
                    ],
                    'region_id' => [
                        'value' => 'California',
                        'input' => 'select',
                    ],
                    'postcode' => [
                        'value' => '90230',
                    ],
                    'country_id' => [
                        'value' => 'United States',
                        'input' => 'select',
                    ],
                ],
            ]
        ];
    }
}
