<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return [
    'services' => [
        'Magento\Customer\Service\V1\CustomerServiceInterface' => [
            'getCustomer' => [
                'resources' => [
                    0 => [
                        'Magento_Customer::customer_self'
                    ],
                    1 => [
                        'Magento_Customer::customer_self'
                    ],
                    2 => [
                        'Magento_Customer::read'
                    ],
                ],
                'secure' => false,
            ],
            'updateCustomer' => [
                'resources' => [
                    0 => [
                        'Magento_Customer::customer_self'
                    ]
                ],
                'secure' => true,
            ],
            'createCustomer' => [
                'resources' => [
                    0 => [
                        'Magento_Customer::manage'
                    ]
                ],
                'secure' => false,
            ],
            'deleteCustomer' => [
                'resources' => [
                    0 => [
                        'Magento_Customer::manage',
                        'Magento_Customer::delete'
                    ]
                ],
                'secure' => false,
            ],
        ],
    ],
    'routes' => [
        '/V1/customers/me/session' => [
            'GET' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\Customer\Service\V1\CustomerServiceInterface',
                    'method' => 'getCustomer',
                ],
                'resources' => [
                    'Magento_Customer::customer_self' => true,
                ],
                'parameters' => [
                    'id' => [
                        'force' => true,
                        'value' => '%customer_id%',
                    ],
                ],
            ],
        ],
        '/V1/customers/me' => [
            'GET' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\Customer\Service\V1\CustomerServiceInterface',
                    'method' => 'getCustomer',
                ],
                'resources' => [
                    'Magento_Customer::customer_self' => true,
                ],
                'parameters' => [
                    'id' => [
                        'force' => true,
                        'value' => null,
                    ],
                ],
            ],
            'PUT' => [
                'secure' => true,
                'service' => [
                    'class' => 'Magento\Customer\Service\V1\CustomerServiceInterface',
                    'method' => 'updateCustomer',
                ],
                'resources' => [
                    'Magento_Customer::customer_self' => true,
                ],
                'parameters' => [
                    'id' => [
                        'force' => false,
                        'value' => null,
                    ],
                ],
            ]
        ],
        '/V1/customers' => [
            'POST' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\Customer\Service\V1\CustomerServiceInterface',
                    'method' => 'createCustomer',
                ],
                'resources' => [
                    'Magento_Customer::manage' => true,
                ],
                'parameters' => [
                ],
            ],
        ],
        '/V1/customers/:id' => [
            'GET' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\Customer\Service\V1\CustomerServiceInterface',
                    'method' => 'getCustomer',
                ],
                'resources' => [
                    'Magento_Customer::read' => true,
                ],
                'parameters' => [
                ],
            ],
            'DELETE' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\Customer\Service\V1\CustomerServiceInterface',
                    'method' => 'deleteCustomer',
                ],
                'resources' => [
                    'Magento_Customer::manage' => true,
                    'Magento_Customer::delete' => true,
                ],
                'parameters' => [
                ],
            ],
        ],
    ],
];
