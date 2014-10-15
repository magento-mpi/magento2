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
                    'etc' => ['path' => 'app/etc'], // this is needed for installation only
                    'var' => ['path' => 'var'], // to set maintenance mode, as well as for normal application runtime
                    'media' => ['path' => 'pub/media'], // for normal application runtime
                    'static' => ['path' => 'pub/static'] // for runtime, but actually depends on mode
                ],
            ],
        ],
    ],
];
