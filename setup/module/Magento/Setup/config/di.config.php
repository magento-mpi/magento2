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
        ],
    ],
    'instance' => [
        'preference' => [
            'Zend\EventManager\EventManagerInterface' => 'EventManager',
            'Zend\ServiceManager\ServiceLocatorInterface' => 'ServiceManager',
        ],
    ],
];
