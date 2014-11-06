<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

use Magento\Setup\Mvc\Bootstrap\InitParamListener;

return [
    'modules' => [
        'Magento\Setup',
    ],
    'module_listener_options' => [
        'module_paths' => [
            __DIR__ . '/../module',
            __DIR__ . '/../vendor',
        ],
        'config_glob_paths' => array(
            __DIR__ . '/autoload/{,*.}{global,local}.php',
        ),
    ],
    'listeners' => ['Magento\Setup\Mvc\Bootstrap\InitParamListener'],
    'service_manager' => [
        'factories' => [
            InitParamListener::BOOTSTRAP_PARAM => 'Magento\Setup\Mvc\Bootstrap\InitParamListener',
        ]
    ],
];
