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
                'users' => 10,
                'loops' => 100,
            ],
        ],
        'scenarios' => [
            'Add to Cart' => [
                'file' => 'testsuite/add_to_cart.jmx',
                'fixtures' => [
                    'testsuite/fixtures/catalog_product.php',
                ],
            ],
            'Advanced Search' => [
                'file' => 'testsuite/advanced_search.jmx',
                'fixtures' => [
                    'testsuite/fixtures/catalog_product.php',
                ],
            ],
            'Category View' => [
                'file' => 'testsuite/category_view.jmx',
                'fixtures' => [
                    'testsuite/fixtures/catalog_category.php',
                ],
            ],
            'Checkout' => [
                'file' => 'testsuite/checkout.jmx',
                'fixtures' => [
                    'testsuite/fixtures/shipping_flatrate_enabled.php',
                    'testsuite/fixtures/catalog_product.php',
                ],
            ],
            'Home Page' => [
                'file' => 'testsuite/home_page.jmx',
            ],
            'Product Edit' => [
                'file' => 'testsuite/product_edit.jmx',
                'fixtures' => [
                    'testsuite/fixtures/catalog_product.php',
                ],
            ],
            'Product View' => [
                'file' => 'testsuite/product_view.jmx',
                'arguments' => [
                    'product_url_key' => 'product-1.html',
                    'product_name'    => 'Product 1',
                ],
                'fixtures' => [
                    'testsuite/fixtures/catalog_product.php',
                ],
            ],
            'Quick Search' => [
                'file' => 'testsuite/quick_search.jmx',
                'fixtures' => [
                    'testsuite/fixtures/catalog_product.php',
                ],
            ],
        ],
    ],
    'report_dir' => 'report',
];
