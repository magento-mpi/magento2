<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return [
    'types' => [
        'config' => [
            'name' => 'config',
            'translate' => 'label,description',
            'instance' => 'Magento\Framework\App\Cache\Type\Config',
            'label' => 'Configuration',
            'description' => 'Cache Description',
        ],
        'layout' => [
            'name' => 'layout',
            'translate' => 'label,description',
            'instance' => 'Magento\Framework\App\Cache\Type\Layout',
            'label' => 'Layouts',
            'description' => 'Layout building instructions.',
        ],
    ]
];
