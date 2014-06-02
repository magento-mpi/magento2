<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

return [
    'router' => [
        'routes' => [
            'index' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Magento\Setup\Controller\IndexController',
                        'action'     => 'index',
                    ]
                ],
            ],
            'license' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/license',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Magento\Setup\Controller\LicenseController',
                        'action'     => 'index',
                    ]
                ],
            ],
            'menu' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/menu',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Magento\Setup\Controller\MenuController',
                        'action'     => 'index',
                    ]
                ],
            ],
            'check-environment' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/check-environment',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Magento\Setup\Controller\EnvironmentController',
                        'action'     => 'index',
                    ]
                ],
            ],
            'configuration-magento' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/configuration-magento',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Magento\Setup\Controller\ConfigurationController',
                        'action'     => 'index',
                    ]
                ],
            ],
            'add-admin-user' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/add-admin-user',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Magento\Setup\Controller\UserController',
                        'action'     => 'index',
                    ]
                ],
            ],
            'access-to-database' => [
                'type' => 'literal',
                'options' => [
                    'route'    => '/access-to-database',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Magento\Setup\Controller\DatabaseController',
                        'action'     => 'index',
                    ]
                ],
            ],
        ],
    ],
];