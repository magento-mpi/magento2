<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'types' => array(
        'config' => array(
            'name' => 'config',
            'translate' => 'label,description',
            'instance' => 'Magento\Core\Model\Cache\Type\Config',
            'label' => 'Configuration',
            'description' => 'System(config.xml, local.xml) and modules configuration files(config.xml).'
        ),
        'layout' => array(
            'name' => 'layout',
            'translate' => 'label,description',
            'instance' => 'Magento\Core\Model\Cache\Type\Layout',
            'label' => 'Layouts',
            'description' => 'Layout building instructions.'
        ),
    )
);
