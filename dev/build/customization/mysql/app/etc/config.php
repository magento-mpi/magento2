<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    /**
     * Console installer options
     * @see \Magento\Install\Model\Installer\Console::_installOptions
     */
    'install_options' => array(
        'license_agreement_accepted' => 'yes',
        'locale'                     => 'en_US',
        'timezone'                   => 'America/Los_Angeles',
        'default_currency'           => 'USD',
        'db_host'                    => '{{db_host}}',
        'db_name'                    => '{{db_name}}',
        'db_user'                    => '{{db_user}}',
        'db_pass'                    => '{{db_password}}',
        'url'                        => '{{url}}',
        'secure_base_url'            => '{{secure_url}}',
        'use_secure'                 => 'no',
        'use_secure_admin'           => 'no',
        'use_rewrites'               => 'no',
        'admin_lastname'             => 'Admin',
        'admin_firstname'            => 'Admin',
        'admin_email'                => 'admin@example.com',
        'admin_username'             => 'admin',
        'admin_password'             => '123123q' // must be at least of 7 both numeric and alphanumeric characters
    ),
    'report_dir' => 'report'
);
