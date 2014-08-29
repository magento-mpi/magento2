<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

return [
    'console' => [
        'router' => [
            'routes' => [
                'show' => [
                    'options' => [
                        'route' => '[--show_locales] [--show_currencies] [--show_timezones]',
                        'defaults' => [
                            'controller' => 'Magento\Setup\Controller\ConsoleController',
                            'action' => 'info',
                        ]
                    ],
                ],
                'install' => [
                    'options' => [
                        'route' => '[--magentoDir=] --license_agreement_accepted= --db_host= --db_name=' .
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
            ],
        ],
    ],
];