<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Cc Repository
 * Credit cards for checkout
 *
 */
class Cc extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'config' => $defaultConfig,
            'data' => $defaultData
        ];

        $this->_data['visa_default'] = $this->_getVisaDefault();
        $this->_data['visa_direct'] = $this->_getVisaDirect();
        $this->_data['visa_authorizenet'] = $this->_getVisaAuthorizeNet();
        $this->_data['visa_3d_secure_valid'] = $this->_getVisa3dSecureValid();
        $this->_data['visa_3d_secure_invalid'] = $this->_getVisa3dSecureInvalid();
        $this->_data['visa_payflow'] = $this->_getVisaPayflow();
    }

    protected function _getVisaDefault()
    {
        return [
            'config' => [
                'constraint' => 'Success',
            ],
            'data' => [
                'fields' => [
                    'cc_type' => [
                        'value' => 'Visa',
                        'input' => 'select'
                    ],
                    'cc_number' => [
                        'value' => '4111111111111111'
                    ],
                    'cc_exp_month' => [
                        'value' => '01 - January',
                        'input' => 'select'
                    ],
                    'cc_exp_year' => [
                        'value' => date('Y') + 1,
                        'input' => 'select'
                    ],
                    'cc_cid' => [
                        'value' => '123'
                    ]
                ]
            ]
        ];
    }

    protected function _getVisaPayflow()
    {
        return [
            'config' => [
                'constraint' => 'Success',
            ],
            'data' => [
                'fields' => [
                    'cc_number' => [
                        'value' => '4111111111111111'
                    ],
                    'cc_exp_month' => [
                        'value' => '01',
                    ],
                    'cc_exp_year' => [
                        'value' => date('y') + 1
                    ],
                    'cc_cid' => [
                        'value' => '123'
                    ]
                ]
            ]
        ];
    }

    protected function _getVisaDirect()
    {
        return [
            'config' => [
                'constraint' => 'Success',
            ],
            'data' => [
                'fields' => [
                    'cc_type' => [
                        'value' => 'Visa',
                        'input' => 'select'
                    ],
                    'cc_number' => [
                        'value' => '4617747819866651'
                    ],
                    'cc_exp_month' => [
                        'value' => '01 - January',
                        'input' => 'select'
                    ],
                    'cc_exp_year' => [
                        'value' => date('Y') + 1,
                        'input' => 'select'
                    ],
                    'cc_cid' => [
                        'value' => '123'
                    ]
                ]
            ]
        ];
    }

    protected function _getVisaAuthorizeNet()
    {
        return [
            'config' => [
                'constraint' => 'Success',
            ],
            'data' => [
                'fields' => [
                    'cc_type' => [
                        'value' => 'Visa',
                        'input' => 'select'
                    ],
                    'cc_number' => [
                        'value' => '4007000000027'
                    ],
                    'cc_exp_month' => [
                        'value' => '01 - January',
                        'input' => 'select'
                    ],
                    'cc_exp_year' => [
                        'value' => date('Y') + 1,
                        'input' => 'select'
                    ],
                    'cc_cid' => [
                        'value' => '123'
                    ]
                ]
            ]
        ];
    }

    protected function _getVisa3dSecureValid()
    {
        return [
            'config' => [
                'constraint' => 'Success',
            ],
            'data' => [
                'fields' => [
                    'cc_type' => [
                        'value' => 'Visa',
                        'input' => 'select'
                    ],
                    'cc_number' => [
                        'value' => '4000000000000002'
                    ],
                    'cc_exp_month' => [
                        'value' => '01 - January',
                        'input' => 'select'
                    ],
                    'cc_exp_year' => [
                        'value' => date('Y') + 2,
                        'input' => 'select'
                    ],
                    'cc_cid' => [
                        'value' => '123'
                    ],
                ],
                'validation' => [
                    'password' => [
                        'value' => '1234'
                    ]
                ]
            ]
        ];
    }

    protected function _getVisa3dSecureInvalid()
    {
        $invalidData = [
            'data' => [
                'fields' => [
                    'cc_number' => [
                        'value' => '4000000000000010'
                    ],
                ],
            ]
        ];

        return array_replace_recursive($this->_getVisa3dSecureValid(), $invalidData);
    }
}
