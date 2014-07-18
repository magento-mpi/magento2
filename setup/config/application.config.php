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
        'Magento\Framework',
    ],
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
    ],
];
