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
            'fixture_files' => 'testsuite/fixtures/{catalog_category,catalog_product}.php',
        ),
    ),
    'scenario' => array(
        'files' => 'testsuite/{' . implode(',', array(
            'add_to_cart', 'advanced_search', 'category_view', 'checkout',
            'home_page', 'product_edit', 'product_view', 'quick_search'
         )) . '}.jmx',
        'common_params' => array(
            'users' => 10,
            'loops' => 100,
        ),
        'scenario_params' => array(
            'testsuite/product_view.jmx' => array(
                'product_url_key' => 'product-1.html',
                'product_name'    => 'Product 1',
            ),
        ),
    ),
    'report_dir' => 'report',
);
