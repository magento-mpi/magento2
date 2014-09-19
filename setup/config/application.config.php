<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

return [
    'modules' => [
        'Magento\Config',
        'Magento\Filesystem',
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
];
