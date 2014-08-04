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
                'Magento\Module\Dependency\ManagerInterface' => 'Magento\Module\Dependency\Manager',
                'Magento\Module\Setup\Connection\AdapterInterface' => 'Magento\Module\Setup\Connection\Adapter',
                'Magento\Module\Resource\ResourceInterface' => 'Magento\Module\Resource\Resource',
                'Magento\Module\ModuleListInterface' => 'Magento\Module\ModuleList'
            ]
        ],
    ],
];
