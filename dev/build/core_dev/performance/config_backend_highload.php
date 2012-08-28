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
                'enable_charts'              => 'no',
                'cleanup_database'           => 'yes',
            ),
            'fixture_files' => array(
                'testsuite/fixtures/catalog_100k_products.php',
            ),
        ),
    ),
    'scenario' => array(
        'files' => array(
            'testsuite/backend.jmx',
        ),
        'common_params' => array(
            'users' => 1,
            'loops' => 100,
        ),
        'scenario_params' => array(
            'testsuite/backend.jmx' => array(
                'products_number'  => 100000,
                'customers_number' => 100000,
            ),
        ),
    ),
    'report_dir' => 'report',
);
