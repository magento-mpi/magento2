<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

return [
    'parameters' => [
        'application' => [
            'base_path' => realpath(__DIR__ . '/../../'),
        ],
        'magento' => [
            'base_path' => realpath(__DIR__ . '/../../../'),
            'filesystem' => [
                'module' => '/app/code/',
                'config' => '/app/etc/',
                'framework' => '/lib/internal/Magento/Framework/',
                'permissions' => [
                    'etc' => [
                        'path' => 'app/etc',
                    ],
                    'var' => [
                        'path' => 'var',
                    ],
                    'cache' => [
                        'path' => 'var/cache',
                    ],
                    'log' => [
                        'path' => 'var/log',
                    ],
                    'session' => [
                        'path' => 'var/session',
                    ],
                ],
            ],
        ],
    ],
];
