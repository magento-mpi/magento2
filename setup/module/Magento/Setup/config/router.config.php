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
    'console' => ['router' => ['routes' => ConsoleController::getRouterConfig()]],
];
