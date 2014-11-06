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
        'installation' => array(
            'options' => array(
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
            ),
            'options_no_value' => array(
                'cleanup_database',
            ),
        ),
    ),
    'scenario' => array(
        'common_config' => array(
            'arguments' => array(
                'users' => 10,
                'loops' => 100,
            ),
        ),
        'scenarios' => array(
            'Add to Cart' => array(
                'file' => 'testsuite/add_to_cart.jmx',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'Advanced Search' => array(
                'file' => 'testsuite/advanced_search.jmx',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'Category View' => array(
                'file' => 'testsuite/category_view.jmx',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_category.php',
                ),
            ),
            'Checkout' => array(
                'file' => 'testsuite/checkout.jmx',
                'fixtures' => array(
                    'testsuite/fixtures/shipping_flatrate_enabled.php',
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'Home Page' => array(
                'file' => 'testsuite/home_page.jmx',
            ),
            'Product Edit' => array(
                'file' => 'testsuite/product_edit.jmx',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'Product View' => array(
                'file' => 'testsuite/product_view.jmx',
                'arguments' => array(
                    'product_url_key' => 'product-1.html',
                    'product_name'    => 'Product 1',
                ),
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'Quick Search' => array(
                'file' => 'testsuite/quick_search.jmx',
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
        ),
    ),
    'report_dir' => 'report',
);
