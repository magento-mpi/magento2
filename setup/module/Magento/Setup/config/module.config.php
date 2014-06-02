<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

return [
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            'setup' => __DIR__ . '/../view',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'ControllerLoader' => 'Magento\Setup\Service\ControllerLoaderFactory',
            'DiStrictAbstractServiceFactory' => 'Magento\Setup\Service\DiStrictAbstractServiceFactoryFactory',
        ],
    ],
];
