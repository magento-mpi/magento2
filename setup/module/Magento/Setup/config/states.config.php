<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return [
    'nav' => [
        [
            'id'          => 'readiness-check',
            'url'         => 'readiness-check',
            'templateUrl' => 'readiness-check',
            'title'       => 'Readiness Check',
            'controller'  => 'readinessCheck',
            'nav-bar'     => true,
        ],
        [
            'id'          => 'add-database',
            'url'         => 'add-database',
            'templateUrl' => 'add-database',
            'title'       => 'Add Database',
            'controller'  => 'addDatabase',
            'nav-bar'     => true,
        ],
        [
            'id'          => 'web-configuration',
            'url'         => 'web-configuration',
            'templateUrl' => 'web-configuration',
            'title'       => 'Web Configuration',
            'controller'  => 'webConfiguration',
            'nav-bar'     => true,
        ],
        [
            'id'          => 'customize-your-store',
            'url'         => 'customize-your-store',
            'templateUrl' => 'customize-your-store',
            'title'       => 'Customize Your Store',
            'controller'  => 'customizeYourStore',
            'nav-bar'     => true,
        ],
        [
            'id'          => 'create-admin-account',
            'url'         => 'create-admin-account',
            'templateUrl' => 'create-admin-account',
            'title'       => 'Create Admin Account',
            'controller'  => 'createAdminAccount',
            'nav-bar'     => true,
        ],
        [
            'id'          => 'install',
            'url'         => 'install',
            'templateUrl' => 'install',
            'title'       => 'Install',
            'controller'  => 'install',
            'nav-bar'     => true,
        ],
    ]
];
