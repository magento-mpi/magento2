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
            'id'          => 'root',
            'nav-bar'     => false,
            'step'        => 0,
            'views'       => ['root' => []]
        ],
        [
            'id'          => 'root.landing',
            'url'         => 'landing',
            'templateUrl' => 'landing',
            'title'       => 'Landing',
            'controller'  => 'landingController',
            'main'        => true,
            'nav-bar'     => false,
            'step'        => 0,
        ],
        [
            'id'          => 'root.readiness-check',
            'url'         => 'readiness-check',
            'templateUrl' => 'readiness-check',
            'title'       => 'Readiness Check',
            'controller'  => 'readinessCheckController',
            'nav-bar'     => true,
            'step'        => 1,
        ],
        [
            'id'          => 'root.readiness-check.progress',
            'url'         => 'readiness-check/progress',
            'templateUrl' => 'readiness-check/progress',
            'title'       => 'Readiness Check',
            'controller'  => 'readinessCheckController',
            'nav-bar'     => false,
            'step'        => 1,
        ],
        [
            'id'          => 'root.add-database',
            'url'         => 'add-database',
            'templateUrl' => 'add-database',
            'title'       => 'Add Database',
            'controller'  => 'addDatabaseController',
            'nav-bar'     => true,
            'step'        => 2,
        ],
        [
            'id'          => 'root.web-configuration',
            'url'         => 'web-configuration',
            'templateUrl' => 'web-configuration',
            'title'       => 'Web Configuration',
            'controller'  => 'webConfigurationController',
            'nav-bar'     => true,
            'step'        => 3,
        ],
        [
            'id'          => 'root.customize-your-store',
            'url'         => 'customize-your-store',
            'templateUrl' => 'customize-your-store',
            'title'       => 'Customize Your Store',
            'controller'  => 'customizeYourStoreController',
            'nav-bar'     => true,
            'step'        => 4,
        ],
        [
            'id'          => 'root.create-admin-account',
            'url'         => 'create-admin-account',
            'templateUrl' => 'create-admin-account',
            'title'       => 'Create Admin Account',
            'controller'  => 'createAdminAccountController',
            'nav-bar'     => true,
            'step'        => 5,
        ],
        [
            'id'          => 'root.install',
            'url'         => 'install',
            'templateUrl' => 'install',
            'title'       => 'Install',
            'controller'  => 'installController',
            'nav-bar'     => true,
            'step'        => 6,
        ],
    ]
];
