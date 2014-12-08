<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return [
    'application' => [
        'url_host' => '{{web_access_host}}',
        'url_path' => '{{web_access_path}}',
        'installation' => [
            'options' => [
                'language'                   => 'en_US',
                'timezone'                   => 'America/Los_Angeles',
                'currency'                   => 'USD',
                'db_host'                    => '{{db_host}}',
                'db_name'                    => '{{db_name}}',
                'db_user'                    => '{{db_user}}',
                'db_pass'                    => '{{db_password}}',
                'use_secure'                 => '0',
                'use_secure_admin'           => '0',
                'use_rewrites'               => '0',
                'admin_lastname'             => 'Admin',
                'admin_firstname'            => 'Admin',
                'admin_email'                => 'admin@example.com',
                'admin_username'             => 'admin',
                'admin_password'             => '123123q',
                'admin_use_security_key'     => '0',
                'backend_frontname'          => 'backend',
            ],
            'options_no_value' => [
                'cleanup_database',
            ],
        ],
    ],
    'scenario' => [
        'common_config' => [
            'arguments' => [
                'users' => 1,
                'loops' => 1,
            ],
            'settings' => [
                'skip_warm_up' => true,
            ],
        ],
        'scenarios' => [
            'Backend Management with Many Entities' => [
                'file' => 'testsuite/backend.jmx',
                'arguments' => [
                    'loops' => 100,
                    'products_number'  => 100000,
                    'customers_number' => 100000,
                    'orders_number' => 100000,
                ],
                'settings' => [
                    'skip_warm_up' => false,
                ],
                'fixtures' => [
                    'testsuite/fixtures/catalog_100k_products.php',
                    'testsuite/fixtures/customer_100k_customers.php',
                    'testsuite/fixtures/sales_100k_orders.php',
                ],
            ],
            'Product Attributes Indexer' => [
                'file' => '/../../shell/indexer.php',
                'fixtures' => [
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                ],
                'arguments' => [
                    'loops' => 3,
                    'reindex' => 'catalog_product_attribute',
                ],
            ],
            'Product Prices Indexer' => [
                'file' => '/../../shell/indexer.php',
                'fixtures' => [
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                ],
                'arguments' => [
                    'loops' => 3,
                    'reindex' => 'catalog_product_price',
                ],
            ],
            'Product Flat Data Indexer' => [
                'file' => '/../../shell/indexer.php',
                'fixtures' => [
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                    'testsuite/fixtures/catalog_product_flat_enabled.php',
                ],
                'arguments' => [
                    'reindex' => 'catalog_product_flat',
                ],
            ],
            'Category Flat Data Indexer' => [
                'file' => '/../../shell/indexer.php',
                'fixtures' => [
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                    'testsuite/fixtures/catalog_category_flat_enabled.php',
                ],
                'arguments' => [
                    'loops' => 10,
                    'reindex' => 'catalog_category_flat',
                ],
            ],
            'Category Products Indexer' => [
                'file' => '/../../shell/indexer.php',
                'fixtures' => [
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                ],
                'arguments' => [
                    'loops' => 3,
                    'reindex' => 'catalog_category_product',
                ],
            ],
            'Stock Status Indexer' => [
                'file' => '/../../shell/indexer.php',
                'fixtures' => [
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                ],
                'arguments' => [
                    'loops' => 5,
                    'reindex' => 'cataloginventory_stock',
                ],
            ],
            'Catalog Search Indexer' => [
                'file' => '/../../shell/indexer.php',
                'fixtures' => [
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                ],
                'arguments' => [
                    'reindex' => 'catalogsearch_fulltext',
                ],
            ],
        ],
    ],
    'report_dir' => 'report',
];
