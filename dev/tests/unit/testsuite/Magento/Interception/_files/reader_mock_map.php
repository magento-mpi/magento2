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
            'Magento\Interception\Custom\Module\Model\Item' => array(
                'plugins' => array(
                    'simple_plugin' => array(
                        'sortOrder' => 10,
                        'instance' => 'Magento\Interception\Custom\Module\Model\ItemPlugin\Simple'
                    )
                )
            )
        )
    ),
    array(
        'backend',
        array(
            'Magento\Interception\Custom\Module\Model\Item' => array(
                'plugins' => array(
                    'advanced_plugin' => array(
                        'sortOrder' => 5,
                        'instance' => 'Magento\Interception\Custom\Module\Model\ItemPlugin\Advanced'
                    )
                )
            ),
            'Magento\Interception\Custom\Module\Model\ItemContainer' => array(
                'plugins' => array(
                    'simple_plugin' => array(
                        'sortOrder' => 15,
                        'instance' => 'Magento\Interception\Custom\Module\Model\ItemContainerPlugin\Simple'
                    )
                )
            )
        )
    ),
    array(
        'frontend',
        array(
            'Magento\Interception\Custom\Module\Model\Item' => array(
                'plugins' => array(
                    'simple_plugin' => array(
                        'disabled' => true,
                    )
                )
            ),
            'Magento\Interception\Custom\Module\Model\Item\Enhanced' => array(
                'plugins' => array(
                    'advanced_plugin' => array(
                        'sortOrder' => 5,
                        'instance' => 'Magento\Interception\Custom\Module\Model\ItemPlugin\Advanced'
                    )
                )
            )
        )
    ),
);
