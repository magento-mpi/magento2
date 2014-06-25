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
            'id'          => 'landing',
            'url'         => '/landing',
            'templateUrl' => 'landing',
            'title'       => 'Landing',
            'controller'  => 'landing',
            'main'        => true,
            'nav-bar'     => false,
            'step'        => 0,
        ],
        [
            'id'          => 'readiness-check',
            'url'         => 'readiness-check',
            'templateUrl' => 'readiness-check',
            'title'       => 'Readiness Check',
            'controller'  => 'readinessCheck',
            'nav-bar'     => true,
            'step'        => 1,
        ],
        [
            'id'          => 'readiness-check.progress',
            'url'         => 'readiness-check/progress',
            'templateUrl' => 'readiness-check/progress',
            'title'       => 'Readiness Check',
            'controller'  => 'readinessCheck',
            'nav-bar'     => false,
            'step'        => 1,
        ],
        [
            'id'          => 'add-database',
            'url'         => 'add-database',
            'templateUrl' => 'add-database',
            'title'       => 'Add Database',
            'controller'  => 'addDatabase',
            'nav-bar'     => true,
            'step'        => 2,
        ],
        [
            'id'          => 'web-configuration',
            'url'         => 'web-configuration',
            'templateUrl' => 'web-configuration',
            'title'       => 'Web Configuration',
            'controller'  => 'webConfiguration',
            'nav-bar'     => true,
            'step'        => 3,
        ],
        [
            'id'          => 'customize-your-store',
            'url'         => 'customize-your-store',
            'templateUrl' => 'customize-your-store',
            'title'       => 'Customize Your Store',
            'controller'  => 'customizeYourStore',
            'nav-bar'     => true,
            'step'        => 4,
        ],
        [
            'id'          => 'create-admin-account',
            'url'         => 'create-admin-account',
            'templateUrl' => 'create-admin-account',
            'title'       => 'Create Admin Account',
            'controller'  => 'createAdminAccount',
            'nav-bar'     => true,
            'step'        => 5,
        ],
        [
            'id'          => 'install',
            'url'         => 'install',
            'templateUrl' => 'install',
            'title'       => 'Install',
            'controller'  => 'install',
            'nav-bar'     => true,
            'step'        => 6,
        ],
    ]
];
