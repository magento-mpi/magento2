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
                        'route' => 'show (locales|currencies|timezones|options)',
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'info',
                        ]
                    ],
                ],
                'install' => [
                    'options' => [
                        'route' => 'install [--magentoDir=] --license_agreement_accepted= --db_host= --db_name=' .
                            ' --db_user= [--db_pass=] [--db_table_prefix=] --store_url=' .
                            ' --admin_url= [--secure_store_url=] [--secure_admin_url=]' .
                            ' [--use_rewrites=] [--encryption_key=] --locale=' .
                            ' --timezone= --currency= --admin_lastname= --admin_firstname=' .
                            ' --admin_email= --admin_username= --admin_password=',
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'console',
                        ]
                    ],
                ],
                'install-local' => [
                    'options' => [
                        'route' => 'install local [--magentoDir=] --license_agreement_accepted= --db_host= --db_name=' .
                            ' --db_user= [--db_pass=] [--db_table_prefix=] --store_url=' .
                            ' --admin_url= [--secure_store_url=] [--secure_admin_url=]' .
                            ' [--use_rewrites=] [--encryption_key=] --locale=' .
                            ' --timezone= --currency= --admin_lastname= --admin_firstname=' .
                            ' --admin_email= --admin_username= --admin_password=',
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'installLocal',
                        ]
                    ],
                ],
                'install-schema' => [
                    'options' => [
                        'route' => 'install schema',
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'installSchema',
                        ]
                    ],
                ],
                'install-data' => [
                    'options' => [
                        'route' => 'install data --magentoDir=',
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'installData',
                        ]
                    ],
                ],
            ],
        ],
    ],
];