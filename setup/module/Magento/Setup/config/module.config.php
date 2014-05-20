<?php

return [
    'controllers' => [
        'invokables' => [
            'Magento\Setup\Controller\Index' => 'Magento\Setup\Controller\IndexController',
        ]
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Magento\Setup\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'setup' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/[:controller[/:action]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => [
                        'controller' => 'Magento\Setup\Controller\Index',
                        'action'     => 'index',
                    ],
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
            'magento/index/index'     => __DIR__ . '/../view/magento/setup/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            'setup' => __DIR__ . '/../view',
        ],
    ]
];
