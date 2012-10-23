<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
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
                'users' => 10,
                'loops' => 100,
            ),
        ),
        'scenarios' => array(
            'testsuite/add_to_cart.jmx' => array(
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'testsuite/advanced_search.jmx' => array(
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'testsuite/category_view.jmx' => array(
                'fixtures' => array(
                    'testsuite/fixtures/catalog_category.php',
                ),
            ),
            'testsuite/checkout.jmx' => array(
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'testsuite/home_page.jmx',
            'testsuite/product_edit.jmx' => array(
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'testsuite/product_view.jmx' => array(
                'arguments' => array(
                    'product_url_key' => 'product-1.html',
                    'product_name'    => 'Product 1',
                ),
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'testsuite/quick_search.jmx' => array(
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
            'testsuite/quick_search.jmx' => array(
                'fixtures' => array(
                    'testsuite/fixtures/catalog_product.php',
                ),
            ),
        ),
    ),
    'report_dir' => 'report',
);
