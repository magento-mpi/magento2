<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

use Magento\Setup\Controller\ConsoleController;

return [
    'route_manager' => [
        'invokables' => [
            'setup' => 'Magento\Setup\Mvc\Router\Http\Setup',
        ],
    ],
    'router' => [
        'routes' => [
            'setup' => [
                'type' => 'setup',
                'options' => [
                    'regex'    => '\b(?<lang>[\w]+).*\/(?<controller>[\w-\/]+)$',
                    'defaults' => [
                        '__NAMESPACE__' => 'Magento\Setup\Controller',
                        'action' => 'index',
                    ],
                    'spec' => '%controller%',
                ],
            ],
            'home' => [
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
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'show' => [
                    'options' => [
                        'route' => ConsoleController::getCliRoute(ConsoleController::CMD_INFO),
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'info',
                        ]
                    ],
                ],
                'install' => [
                    'options' => [
                        'route' => ConsoleController::getCliRoute(ConsoleController::CMD_INSTALL),
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'install',
                        ]
                    ],
                ],
                'install-configuration' => [
                    'options' => [
                        'route' => ConsoleController::getCliRoute(ConsoleController::CMD_INSTALL_CONFIG),
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'installDeploymentConfig',
                        ]
                    ],
                ],
                'install-schema' => [
                    'options' => [
                        'route' => ConsoleController::getCliRoute(ConsoleController::CMD_INSTALL_SCHEMA),
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'installSchema',
                        ]
                    ],
                ],
                'install-data' => [
                    'options' => [
                        'route' => ConsoleController::getCliRoute(ConsoleController::CMD_INSTALL_DATA),
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'installData',
                        ]
                    ],
                ],
                'install-user-configuration' => [
                    'options' => [
                        'route' => ConsoleController::getCliRoute(ConsoleController::CMD_INSTALL_USER_CONFIG),
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'installUserConfig',
                        ]
                    ],
                ],
                'install-admin-user' => [
                    'options' => [
                        'route' => ConsoleController::getCliRoute(ConsoleController::CMD_INSTALL_ADMIN_USER),
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'installAdminUser',
                        ]
                    ],
                ],
            ],
        ],
    ],
];
