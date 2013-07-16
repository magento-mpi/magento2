<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'preferences' => array(
        'Mage_Core_Model_Db_UpdaterInterface' => 'Mage_Core_Model_Db_Updater',
        'Mage_Core_Model_AppInterface' => 'Mage_Core_Model_App_Proxy',
    ),

    'Mage_Core_Model_App_State' => array(
        'parameters' => array(
            'mode' => array(
                'argument' => 'Mage::PARAM_MODE',
            ),
        ),
    ),

    'Mage_Core_Model_Config_Modules' => array(
        'parameters' => array(
            'storage' => array(
                'instance' => 'Mage_Core_Model_Config_Storage_Modules',
                'shared' => false
            ),
        ),
        'plugins' => array(
            'simple_modules_plugin' => array(
                'instance' => 'Mage_Core_Model_Config_Modules_Plugin',
                'sortOrder' => 10,
                'disabled' => true,
            ),
            'simple_modules_plugin_advanced' => array(
                'instance' => 'Mage_Core_Model_Config_Modules_PluginAdvanced',
                'sortOrder' => 0,
            ),
            'overridden_plugin' => array(
                'sortOrder' => 30,
                'disabled' => true,
            ),
        ),
    ),

    'Magento_Http_Handler_Composite' => array(
        'shared' => false,
        'parameters' => array(
            'factory' => array(
                'instance' => 'Magento_Http_HandlerFactory',
            ),
            'handlers' => array(
                'custom_handler' => array(
                    'sortOrder' => 25,
                    'class' => 'Custom_Cache_Model_Http_Handler',
                ),
                'other_handler' => array(
                    'sortOrder' => 10,
                    'class' => 'Other_Cache_Model_Http_Handler',
                ),
            ),
        ),
    ),

    'Varien_Data_Collection_Db_FetchStrategy_Cache' => array(
        'parameters' => array(
            'cacheIdPrefix' => 'collection_',
            'cacheLifetime' => '86400',
        ),
    ),

    'customCacheInstance' => array(
        'shared' => true,
        'type' => 'Mage_Core_Model_Cache',
        'parameters' => array(),
    ),

    'customOverriddenInstance' => array(
        'shared' => false,
        'parameters' => array(),
    ),
);
