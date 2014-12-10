<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
return [
    'application' => [
        'url_host' => '{{web_access_host}}',
        'url_path' => '{{web_access_path}}',
        'installation' => [
            'options' => [
                'language'                   => 'en_US',
                'timezone'                   => 'America/Los_Angeles',
                'currency'                   => 'USD',
                'db_host'                    => '{{db_host}}',
                'db_name'                    => '{{db_name}}',
                'db_user'                    => '{{db_user}}',
                'db_pass'                    => '{{db_password}}',
                'use_secure'                 => '0',
                'use_secure_admin'           => '0',
                'use_rewrites'               => '0',
                'admin_lastname'             => 'Admin',
                'admin_firstname'            => 'Admin',
                'admin_email'                => 'admin@example.com',
                'admin_username'             => 'admin',
                'admin_password'             => '123123q',
                'admin_use_security_key'     => '0',
                'backend_frontname'          => 'backend',
            ],
            'options_no_value' => [
                'cleanup_database',
            ],
        ],
    ],
    'scenario' => [
        'common_config' => [
            /* Common arguments passed to all scenarios */
            'arguments' => [
                'users'       => 100,
                'loops'       => 1,
                'ramp_period' => 120,
            ],
            /* Common settings for all scenarios */
            'settings' => [],
        ],
    ],
    'report_dir' => 'report',
];
