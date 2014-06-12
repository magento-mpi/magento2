<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

return [
    'modules' => [
        'Magento\Install',
        'Magento\Config',
        'Magento\Module',
        'Magento\Setup',
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
