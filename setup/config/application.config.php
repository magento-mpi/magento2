<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

return [
    'modules' => [
        'Magento\Filesystem',
        'Magento\Setup',
    ],
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],
    ],
];
