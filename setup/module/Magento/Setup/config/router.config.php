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
                        'route' => 'show (locales|currencies|timezones|options):type',
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'info',
                        ]
                    ],
                ],
                'install-local' => [
                    'options' => [
                        'route' => 'install local --license_agreement_accepted= --db_host= --db_name=' .
                            ' --db_user= --admin_url= [--db_pass=] [--db_table_prefix=] [--magentoDir=]',
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'installLocal',
                        ]
                    ],
                ],
                'install-schema' => [
                    'options' => [
                        'route' => 'install schema [--magentoDir=]',
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'installSchema',
                        ]
                    ],
                ],
                'install-data' => [
                    'options' => [
                        'route' => 'install data --store_url= ' .
                            ' --locale= --timezone= --currency= --admin_lastname= --admin_firstname= '.
                            ' --admin_email= --admin_username= --admin_password='.
                            ' [--secure_admin_url=] [--use_rewrites=]'.
                            ' [--encryption_key=] [--secure_store_url=] [--magentoDir=]',
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