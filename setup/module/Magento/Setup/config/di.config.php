<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

return [
    'di' => [
        'allowed_controllers' => [
            'Magento\Setup\Controller\ConfigurationController',
            'Magento\Setup\Controller\DatabaseController',
            'Magento\Setup\Controller\EnvironmentController',
            'Magento\Setup\Controller\IndexController',
            'Magento\Setup\Controller\LicenseController',
            'Magento\Setup\Controller\MenuController',
            'Magento\Setup\Controller\UserController',
            'Magento\Setup\Controller\TestController',
        ],
        'instance' => [
            'Magento\Setup\Controller\TestController' => [
                'parameters' => [
                    'resolver' => new \Magento\Filesystem\Resolver\ByPattern(
                            new \Magento\Filesystem\GlobWrapper(),
                            realpath(__DIR__ . '/../../../../../app/code'),
                            '/*/*/sql/*/install-*.php'
                        ),
                ],
            ],
        ],
    ],
    'instance' => [
        'preference' => [
            'Zend\EventManager\EventManagerInterface' => 'EventManager',
            'Zend\ServiceManager\ServiceLocatorInterface' => 'ServiceManager',
        ],
    ],
];
