<?php

return [
    'controllers' => [
        'invokables' => [
            'index'   => 'Magento\Setup\Controller\IndexController',
            'license' => 'Magento\Setup\Controller\LicenseController',
            'menu'    => 'Magento\Setup\Controller\MenuController',
            'check-environment' => 'Magento\Setup\Controller\EnvironmentController',
            'configuration-magento' => 'Magento\Setup\Controller\ConfigurationController',
            'add-admin-user' => 'Magento\Setup\Controller\UserController',
            'access-to-database' => 'Magento\Setup\Controller\DatabaseController',
        ]
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'segment',
                'options' => [
                    'route'    => '/[:controller]',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'index',
                        'action'     => 'invoke',
                    ]
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'magento/index/invoke'    => __DIR__ . '/../view/magento/setup/index/index.phtml',
            'magento/license/invoke'  => __DIR__ . '/../view/magento/setup/license/index.phtml',
            'magento/environment/invoke'  => __DIR__ . '/../view/magento/setup/environment/index.phtml',
            'magento/database/invoke'  => __DIR__ . '/../view/magento/setup/database/index.phtml',
            'magento/configuration/invoke'  => __DIR__ . '/../view/magento/setup/configuration/index.phtml',
            'magento/user/invoke'  => __DIR__ . '/../view/magento/setup/user/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            'setup' => __DIR__ . '/../view',
        ],
    ]
];
