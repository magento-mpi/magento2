<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  configuration
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'installer_options' => array(
        '--license_agreement_accepted', 'yes',
        '--locale',                     'en_US',
        '--timezone',                   'America/Los_Angeles',
        '--default_currency',           'USD',
        '--db_host',                    'localhost',
        '--db_name',                    'bamboo_functional',
        '--db_user',                    'root',
        '--db_pass',                    '',
        '--url',                        'http://mage-dev.varien.com/dev/bamboo-functional-tests/',
        '--secure_base_url',            'https://mage-dev.varien.com/dev/bamboo-functional-tests/',
        '--use_secure',                 'yes',
        '--use_secure_admin',           'yes',
        '--use_rewrites',               'yes',
        '--admin_lastname',             'Admin',
        '--admin_firstname',            'Admin',
        '--admin_email',                'admin@example.com',
        '--admin_username',             'admin',
        '--admin_password',             '123123q',
    ),
    'config_data' => array(
        'admin/security/use_form_key' => '0',
    ),
);
