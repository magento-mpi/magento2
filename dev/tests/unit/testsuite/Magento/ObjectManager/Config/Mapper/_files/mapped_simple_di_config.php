<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'preferences' => array(
        'Magento_Core_Model_Db_UpdaterInterface' => 'Magento_Core_Model_Db_Updater',
        'Magento_Core_Model_AppInterface' => 'Magento_Core_Model_App_Proxy',
    ),

    'Magento_Core_Model_App_State' => array(
        'parameters' => array(
            'mode' => array(
                'argument' => 'MAGE_MODE',
            ),
        ),
    ),

    'Magento_Core_Model_Config_Modules' => array(
        'parameters' => array(
            'storage' => array(
                'instance' => 'Magento_Core_Model_Config_Storage_Modules',
                'shared' => false
            ),
        ),
        'plugins' => array(
            'simple_modules_plugin' => array(
                'sortOrder' => 10,
                'disabled' => true,
                'instance' => 'Magento_Core_Model_Config_Modules_Plugin',
            ),
            'simple_modules_plugin_advanced' => array(
                'sortOrder' => 0,
                'instance' => 'Magento_Core_Model_Config_Modules_PluginAdvanced',
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
            'cacheTags' => array(
                'const' => Magento_Core_Model_Website::CACHE_TAG,
                'boolFalse' => false, 
                'boolTrue' => true,
                'boolOne' => true,
                'boolZero' => false,
                'intValue' => 100500,
                'nullValue' => null,
                'stringPattern' => 'az-value',
            ),
            'constParam' => 'website',
            'boolFalseParam' => false,
            'boolTrueParam' => true,
            'boolOneParam' => true,
            'boolZeroParam' => false,
            'intValueParam' => 100500,
            'nullValueParam' => null,
            'stringPatternParam' => 'az-value',
        ),
    ),

    'customCacheInstance' => array(
        'shared' => true,
        'type' => 'Magento_Core_Model_Cache',
        'parameters' => array(),
    ),

    'customOverriddenInstance' => array(
        'shared' => false,
        'parameters' => array(),
    ),
);
