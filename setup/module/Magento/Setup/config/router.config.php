<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

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
                    'route'    => '/[:controller]',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Magento\Setup\Controller',
                        'action' => 'index',
                    ],
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
];