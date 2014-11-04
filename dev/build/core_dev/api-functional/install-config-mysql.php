<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

return [
    'db_host'                => '{{db_host}}',
    'db_user'                => '{{db_user}}',
    'db_pass'                => '{{db_password}}',
    'db_name'                => '{{db_name}}',
    'db_prefix'              => '{{db_table_prefix}}',
    'backend_frontname'      => 'backend',
    'base_url'               => '{{url}}/',
    'base_url_secure'        => '{{secure_url}}/',
    'session_save'           => 'db',
    'admin_username'         => \Magento\TestFramework\Bootstrap::ADMIN_NAME,
    'admin_password'         => \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD,
    'admin_email'            => \Magento\TestFramework\Bootstrap::ADMIN_EMAIL,
    'admin_firstname'        => \Magento\TestFramework\Bootstrap::ADMIN_FIRSTNAME,
    'admin_lastname'         => \Magento\TestFramework\Bootstrap::ADMIN_LASTNAME,
    'admin_use_security_key' => '0',
    'use_rewrites'           => '0',
    'cleanup_database'       => true,
];
