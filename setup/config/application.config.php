<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

return [
    'modules' => [
        'Magento\Composer',
        'Magento\Config',
        'Magento\Filesystem',
        'Magento\Locale',
        'Magento\Module',
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
