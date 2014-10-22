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
     */
    'install_options' => array(
        'language'                   => 'en_US',
        'timezone'                   => 'America/Los_Angeles',
        'currency'                   => 'USD',
        'db_host'                    => '{{db_host}}',
        'db_name'                    => '{{db_name}}',
        'db_user'                    => '{{db_user}}',
        'db_pass'                    => '{{db_password}}',
        'base_url'                   => '{{url}}',
        'base_url_secure'            => '{{secure_url}}',
        'backend_frontname'          => 'backend',
        'use_secure'                 => '0',
        'use_secure_admin'           => '0',
        'use_rewrites'               => '0',
        'admin_lastname'             => 'Admin',
        'admin_firstname'            => 'Admin',
        'admin_email'                => 'admin@example.com',
        'admin_username'             => 'admin',
        'admin_password'             => '123123q' // must be at least of 7 both numeric and alphanumeric characters
    ),
    'report_dir' => 'report'
);
