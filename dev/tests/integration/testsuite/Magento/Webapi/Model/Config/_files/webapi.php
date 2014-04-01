<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return [
    'services' => [
        'Magento\TestModule1\Service\V1\AllSoapAndRestInterface' => [
            'item' => [
                'resources' => [
                    'Magento_Test1::resource1' => true,
                ],
                'secure' => false,
            ],
            'create' => [
                'resources' => [
                    'Magento_Test1::resource1' => true,
                ],
                'secure' => false,
            ],
        ],
        'Magento\TestModule1\Service\V2\AllSoapAndRestInterface' => [
            'item' => [
                'resources' => [
                    'Magento_Test1::resource1' => true,
                    'Magento_Test1::resource2' => true,
                ],
                'secure' => false,
            ],
            'create' => [
                'resources' => [
                    'Magento_Test1::resource1' => true,
                    'Magento_Test1::resource2' => true,
                ],
                'secure' => false,
            ],
            'delete' => [
                'resources' => [
                    'Magento_Test1::resource1' => true,
                    'Magento_Test1::resource2' => true,
                ],
                'secure' => false,
            ],
            'update' => [
                'resources' => [
                    'Magento_Test1::resource1' => true,
                    'Magento_Test1::resource2' => true,
                ],
                'secure' => false,
            ],
        ],
    ],
    'routes' => [
        '/V1/testmodule1/:id' => [
            'GET' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\TestModule1\Service\V1\AllSoapAndRestInterface',
                    'method' => 'item',
                ],
                'resources' => [
                    'Magento_Test1::resource1' => true,
                ],
                'parameters' => [
                ],
            ],
        ],
        '/V2/testmodule1/:id' => [
            'GET' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\TestModule1\Service\V2\AllSoapAndRestInterface',
                    'method' => 'item',
                ],
                'resources' => [
                    'Magento_Test1::resource1' => true,
                    'Magento_Test1::resource2' => true,
                ],
                'parameters' => [
                ],
            ],
            'DELETE' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\TestModule1\Service\V2\AllSoapAndRestInterface',
                    'method' => 'delete',
                ],
                'resources' => [
                    'Magento_Test1::resource1' => true,
                    'Magento_Test1::resource2' => true,
                ],
                'parameters' => [
                ],
            ],
            'PUT' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\TestModule1\Service\V2\AllSoapAndRestInterface',
                    'method' => 'update',
                ],
                'resources' => [
                    'Magento_Test1::resource1' => true,
                    'Magento_Test1::resource2' => true,
                ],
                'parameters' => [
                ],
            ],
        ],
        '/V2/testmodule1' => [
            'POST' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\TestModule1\Service\V2\AllSoapAndRestInterface',
                    'method' => 'create',
                ],
                'resources' => [
                    'Magento_Test1::resource1' => true,
                    'Magento_Test1::resource2' => true,
                ],
                'parameters' => [
                    'id' => [
                        'force' => true,
                        'value' => 'null',
                    ]
                ],
            ],
        ],
        '/V1/testmodule1' => [
            'POST' => [
                'secure' => false,
                'service' => [
                    'class' => 'Magento\TestModule1\Service\V1\AllSoapAndRestInterface',
                    'method' => 'create',
                ],
                'resources' => [
                    'Magento_Test1::resource1' => true,
                ],
                'parameters' => [
                    'id' => [
                        'force' => true,
                        'value' => 'null',
                    ]
                ],
            ],
        ],
    ],
];
