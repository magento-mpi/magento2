<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    /**
     * Console installer options
     * @see \Magento\Install\Model\Installer\Console::installParameters
     */
    'install_options' => array(
        'license_agreement_accepted' => 'yes',
        'locale'                     => 'en_US',
        'timezone'                   => 'America/Los_Angeles',
        'default_currency'           => 'USD',
        'db_model'                   => '{{db_model}}',
        'db_host'                    => '{{db_host}}',
        'db_name'                    => '{{db_name}}',
        'db_user'                    => '{{db_user}}',
        'db_pass'                    => '{{db_password}}',
        'use_secure'                 => 'yes',
        'use_secure_admin'           => 'yes',
        'admin_no_form_key'          => 'yes',
        'use_rewrites'               => 'yes',
        'admin_lastname'             => 'Admin',
        'admin_firstname'            => 'Admin',
        'admin_email'                => 'admin@example.com',
        'admin_username'             => 'admin',
        'admin_password'             => '123123q', // must be at least of 7 both numeric and alphanumeric characters
        'url'                        => '{{url}}',
        'secure_base_url'            => '{{secure_url}}',
        'session_save'               => 'db',
        'cleanup_database'           => true,
    )
);
