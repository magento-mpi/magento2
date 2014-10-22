<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    /**
     * Console installer options
     * @see \Magento\Install\Model\Installer\Console::installParameters
     */
    'install_options' => array(
        'language'               => 'en_US',
        'timezone'               => 'America/Los_Angeles',
        'currency'               => 'USD',
        'db_model'               => '{{db_model}}',
        'db_host'                => '{{db_host}}',
        'db_name'                => '{{db_name}}',
        'db_user'                => '{{db_user}}',
        'db_pass'                => '{{db_password}}',
        'use_secure'             => '1',
        'use_secure_admin'       => '1',
        'admin_use_security_key' => '0',
        'use_rewrites'           => '1',
        'admin_lastname'         => 'Admin',
        'admin_firstname'        => 'Admin',
        'admin_email'            => 'admin@example.com',
        'admin_username'         => 'admin',
        'admin_password'         => '123123q', // must be at least of 7 both numeric and alphanumeric characters
        'base_url'               => '{{url}}',
        'base_url_secure'        => '{{secure_url}}',
        'session_save'           => 'db',
        'backend_frontname'      => 'backend',
    )
);
