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
        'url_host' => 'mage2.magentocommerce.com',
        'url_path' => '/bamboo-agent/',
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
                'db_host'                    => 'localhost',
                'db_name'                    => 'bamboo_performance',
                'db_user'                    => 'root',
                'db_pass'                    => '',
                'use_secure'                 => 'no',
                'use_secure_admin'           => 'no',
                'use_rewrites'               => 'no',
                'admin_lastname'             => 'Admin',
                'admin_firstname'            => 'Admin',
                'admin_email'                => 'admin@example.com',
                'admin_no_form_key'          => 'yes',
            ),
            'fixture_files' => 'testsuite/fixtures/*.php',
        ),
    ),
    'scenario' => array(
        'files' => 'testsuite/*.jmx',
        'common_params' => array(
            'users' => 10,
            'loops' => 100,
        ),
        'scenario_params' => array(
            'testsuite/product_view.jmx' => array(
                'product_url_key' => 'product-1.html',
                'product_name'    => 'Product 1',
            ),
            'testsuite/product_edit.jmx' => array(
                'product_sku' => 'product_1',
                'users' => 2,
                'loops' => 100,
            ),
        ),
    ),
    'report_dir' => 'report',
);
