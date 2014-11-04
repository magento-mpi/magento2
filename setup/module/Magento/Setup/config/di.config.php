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
            'Magento\Setup\Controller\ReadinessCheckController',
            'Magento\Setup\Controller\ReadinessCheck\ProgressController',
            'Magento\Setup\Controller\AddDatabaseController',
            'Magento\Setup\Controller\WebConfigurationController',
            'Magento\Setup\Controller\CustomizeYourStoreController',
            'Magento\Setup\Controller\CreateAdminAccountController',
            'Magento\Setup\Controller\SuccessController',
            'Magento\Setup\Controller\Success\EncryptionController',
            'Magento\Setup\Controller\InstallController',
            'Magento\Setup\Controller\Install\ProgressController',
            'Magento\Setup\Controller\Install\ClearProgressController',
            'Magento\Setup\Controller\Install\StartController',
            'Magento\Setup\Controller\IndexController',
            'Magento\Setup\Controller\LandingController',
            'Magento\Setup\Controller\LicenseController',
            'Magento\Setup\Controller\EnvironmentController',
            'Magento\Setup\Controller\UserController',
            'Magento\Setup\Controller\ConsoleController',

            'Magento\Setup\Controller\Controls\HeaderController',
            'Magento\Setup\Controller\Controls\MenuController',
            'Magento\Setup\Controller\Controls\NavbarController',

            'Magento\Setup\Controller\Data\FilePermissionsController',
            'Magento\Setup\Controller\Data\PhpExtensionsController',
            'Magento\Setup\Controller\Data\PhpVersionController',
            'Magento\Setup\Controller\Data\StatesController',
            'Magento\Setup\Controller\Data\DatabaseController',
            'Magento\Setup\Controller\Data\LanguagesController',
        ],
        'instance' => [
            'preference' => [
                'Zend\EventManager\EventManagerInterface' => 'EventManager',
                'Zend\ServiceManager\ServiceLocatorInterface' => 'ServiceManager',
                'Magento\Framework\Module\DependencyManagerInterface' => 'Magento\Framework\Module\DependencyManager',
                'Magento\Setup\Module\Resource\ResourceInterface' => 'Magento\Setup\Module\Resource\Resource',
                'Magento\Setup\Module\ModuleListInterface' => 'Magento\Setup\Module\ModuleList',
            ]
        ],
    ],
];
