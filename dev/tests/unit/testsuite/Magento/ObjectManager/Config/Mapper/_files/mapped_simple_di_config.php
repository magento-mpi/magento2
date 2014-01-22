<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'preferences' => array(
        'Magento\Module\UpdaterInterface' => 'Magento\Module\Updaterter',
        'Magento\AppInterface' => 'Magento\Core\Model\App\Proxy',
    ),

    'Magento\App\State' => array(
        'parameters' => array(
            'mode' => array(
                'argument' => 'MAGE_MODE',
            ),
        ),
    ),

    'Magento\Core\Model\Config_Modules' => array(
        'parameters' => array(
            'storage' => array(
                'instance' => 'Magento\Core\Model\Config\Storage_Modules',
                'shared' => false
            ),
        ),
        'plugins' => array(
            'simple_modules_plugin' => array(
                'sortOrder' => 10,
                'disabled' => true,
                'instance' => 'Magento\Core\Model\Config_Modules_Plugin',
            ),
            'simple_modules_plugin_advanced' => array(
                'sortOrder' => 0,
                'instance' => 'Magento\Core\Model\Config_Modules_PluginAdvanced',
            ),
            'overridden_plugin' => array(
                'sortOrder' => 30,
                'disabled' => true,
            ),
        ),
    ),

    'Magento\Http\Handler\Composite' => array(
        'shared' => false,
        'parameters' => array(
            'factory' => array(
                'instance' => 'Magento\Http\HandlerFactory',
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

    'Magento\Data\Collection\Db\FetchStrategy\Cache' => array(
        'parameters' => array(
            'cacheIdPrefix' => 'collection_',
            'cacheLifetime' => '86400',
            'cacheTags' => array(
                'const' => \Magento\Core\Model\Website::CACHE_TAG,
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
        'type' => 'Magento\App\Cache',
        'parameters' => array(),
    ),

    'customOverriddenInstance' => array(
        'shared' => false,
        'parameters' => array(),
    ),
);
