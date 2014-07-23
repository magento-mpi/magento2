<?php
/**
 * Magento console installer options for Web API functional tests. Are used in functional tests bootstrap.
 *
 * @see \Magento\Install\Model\Installer\Console::_installOptions
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'install_options' => array(
        'license_agreement_accepted' => 'yes',
        'locale'                     => 'en_US',
        'timezone'                   => 'America/Los_Angeles',
        'default_currency'           => 'USD',
        'db_host'                    => 'localhost',
        'db_name'                    => 'magento_functional_tests',
        'db_user'                    => 'magento',
        'db_pass'                    => '123123q',
        'url'                        => 'http://mage.com/magento2/',
        'secure_base_url'            => 'http://mage.com/magento2/',
        'use_secure'                 => 'no',
        'use_secure_admin'           => 'no',
        'use_rewrites'               => 'no',
        'admin_lastname'             => 'Admin',
        'admin_firstname'            => 'Admin',
        'admin_email'                => 'admin@example.com',
        'admin_username'             => 'admin',
        'admin_password'             => '123123q',
        'admin_no_form_key'          => 'yes',
        /* PayPal has limitation for order number - 20 characters. 10 digits prefix + 8 digits number is good enough */
        'order_increment_prefix'     => time(),
    )
);
