<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

return [
    'install_options' => [
        'language'               => 'en_US',
        'timezone'               => 'America/Los_Angeles',
        'currency'               => 'USD',
        'db_model'               => '{{db_model}}',
        'db_host'                => '{{db_host}}',
        'db_name'                => '{{db_name}}',
        'db_user'                => '{{db_user}}',
        'db_pass'                => '{{db_password}}',
        'admin_use_security_key' => '0',
        'use_rewrites'           => '1',
        'admin_lastname'         => 'Admin',
        'admin_firstname'        => 'Admin',
        'admin_email'            => 'admin@example.com',
        'admin_username'         => 'admin',
        'admin_password'         => '123123q', // must be at least of 7 both numeric and alphanumeric characters
        'base_url'               => '{{url}}',
        'session_save'           => 'db',
        'backend_frontname'      => 'backend',
    ],
    'install_options_no_value' => [
        'cleanup_database',
    ]
];
