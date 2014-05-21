<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array(
        'global',
        array(
            'Magento\Framework\Interception\Custom\Module\Model\Item' => array(
                'plugins' => array(
                    'simple_plugin' => array(
                        'sortOrder' => 10,
                        'instance' => 'Magento\Framework\Interception\Custom\Module\Model\ItemPlugin\Simple'
                    )
                )
            )
        )
    ),
    array(
        'backend',
        array(
            'Magento\Framework\Interception\Custom\Module\Model\Item' => array(
                'plugins' => array(
                    'advanced_plugin' => array(
                        'sortOrder' => 5,
                        'instance' => 'Magento\Framework\Interception\Custom\Module\Model\ItemPlugin\Advanced'
                    )
                )
            ),
            'Magento\Framework\Interception\Custom\Module\Model\ItemContainer' => array(
                'plugins' => array(
                    'simple_plugin' => array(
                        'sortOrder' => 15,
                        'instance' => 'Magento\Framework\Interception\Custom\Module\Model\ItemContainerPlugin\Simple'
                    )
                )
            )
        )
    ),
    array(
        'frontend',
        array(
            'Magento\Framework\Interception\Custom\Module\Model\Item' => array(
                'plugins' => array('simple_plugin' => array('disabled' => true))
            ),
            'Magento\Framework\Interception\Custom\Module\Model\Item\Enhanced' => array(
                'plugins' => array(
                    'advanced_plugin' => array(
                        'sortOrder' => 5,
                        'instance' => 'Magento\Framework\Interception\Custom\Module\Model\ItemPlugin\Advanced'
                    )
                )
            ),
            'SomeType' => array(
                'plugins' => array(
                    'simple_plugin' => array(
                        'instance' => 'NonExistingPluginClass'
                    )
                )
            )
        )
    )
);
