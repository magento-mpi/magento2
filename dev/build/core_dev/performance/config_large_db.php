<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'application' => array(
        'url_host' => '{{web_access_host}}',
        'url_path' => '{{web_access_path}}',
        'admin' => array(
            'frontname' => 'backend',
            'username'  => 'admin',
            'password'  => '123123q',
        ),
        'installation' => array(
            'options' => array(
                'license_agreement_accepted' => 'yes',
                'locale'                     => 'en_US',
                'timezone'                   => 'America/Los_Angeles',
                'default_currency'           => 'USD',
                'db_host'                    => '{{db_host}}',
                'db_name'                    => '{{db_name}}',
                'db_user'                    => '{{db_user}}',
                'db_pass'                    => '{{db_password}}',
                'use_secure'                 => 'no',
                'use_secure_admin'           => 'no',
                'use_rewrites'               => 'no',
                'admin_lastname'             => 'Admin',
                'admin_firstname'            => 'Admin',
                'admin_email'                => 'admin@example.com',
                'admin_no_form_key'          => 'yes',
                'cleanup_database'           => 'yes',
            ),
        ),
    ),
    'scenario' => array(
        'common_config' => array(
            'arguments' => array(
                'users' => 1,
                'loops' => 1,
            ),
            'settings' => array(
                'skip_warm_up' => true,
            ),
        ),
        'scenarios' => array(
            'Backend Management with Many Entities' => array(
                'file' => 'testsuite/backend.jmx',
                'arguments' => array(
                    'loops' => 100,
                    'products_number'  => 100000,
                    'customers_number' => 100000,
                    'orders_number' => 100000,
                ),
                'settings' => array(
                    'skip_warm_up' => false,
                ),
                'fixtures' => array(
                    'testsuite/fixtures/catalog_100k_products.php',
                    'testsuite/fixtures/customer_100k_customers.php',
                    'testsuite/fixtures/sales_100k_orders.php',
                ),
            ),
            'Product Attributes Indexer' => array(
                'file' => '/../../shell/indexer.php',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                ),
                'arguments' => array(
                    'loops' => 3,
                    'reindex' => 'catalog_product_attribute',
                ),
            ),
            'Product Prices Indexer' => array(
                'file' => '/../../shell/indexer.php',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                ),
                'arguments' => array(
                    'loops' => 3,
                    'reindex' => 'catalog_product_price',
                ),
            ),
            'Product Flat Data Indexer' => array(
                'file' => '/../../shell/indexer.php',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                    'testsuite/fixtures/catalog_product_flat_enabled.php',
                ),
                'arguments' => array(
                    'reindex' => 'catalog_product_flat',
                ),
            ),
            'Category Flat Data Indexer' => array(
                'file' => '/../../shell/indexer.php',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                    'testsuite/fixtures/catalog_category_flat_enabled.php',
                ),
                'arguments' => array(
                    'loops' => 10,
                    'reindex' => 'catalog_category_flat',
                ),
            ),
            'Category Products Indexer' => array(
                'file' => '/../../shell/indexer.php',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                ),
                'arguments' => array(
                    'loops' => 3,
                    'reindex' => 'catalog_category_product',
                ),
            ),
            'Stock Status Indexer' => array(
                'file' => '/../../shell/indexer.php',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                ),
                'arguments' => array(
                    'loops' => 5,
                    'reindex' => 'cataloginventory_stock',
                ),
            ),
            'Catalog Search Indexer' => array(
                'file' => '/../../shell/indexer.php',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_200_categories_80k_products.php',
                ),
                'arguments' => array(
                    'reindex' => 'catalogsearch_fulltext',
                ),
            ),
        ),
    ),
    'report_dir' => 'report',
);
