<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

return [
    'di' => [
        'instance' => [
            'preference' => [
                'Magento\Filesystem\Driver\DriverInterface' => 'Magento\Filesystem\Driver\File',
            ],
        ],
    ],
];
