<?php
/**
 * Object Manager config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager_Config extends Mage_Core_Model_ObjectManager_ConfigAbstract
{
    /**
     * Initial configuration required to load main configuration
     *
     * @var array
     */
    protected $_initialConfig = array(
        'preference' => array(
            'Mage_Core_Model_Db_UpdaterInterface' => 'Mage_Core_Model_Db_Updater',
            'Mage_Core_Model_AppInterface' => 'Mage_Core_Model_App_Proxy',
            'Mage_Core_Model_Config_InvalidatorInterface' => 'Mage_Core_Model_Config_Invalidator_Proxy',
        ),
        'Mage_Core_Model_Cache' => array(
            'parameters' => array(
                // @todo remove cache types proxy as soon as deprecated methods of Mage_Core_Model_Cache are removed
                'cacheTypes' => 'Mage_Core_Model_Cache_Types_Proxy',
                'config' => 'Mage_Core_Model_Config_Proxy',
            )
        ),
        'Mage_Core_Model_Config' => array(
            'parameters' => array('storage' => 'Mage_Core_Model_Config_Storage')
        ),
        'Mage_Core_Model_Config_Container' => array(
            'parameters' => array('configCache' => 'Mage_Core_Model_Config_Cache_Proxy')
        ),
        'Mage_Core_Model_Config_Resource' => array(
            'parameters' => array('config' => 'Mage_Core_Model_Config_Primary')
        ),
        'Mage_Core_Model_Config_Locales' => array(
            'parameters' => array('storage' => 'Mage_Core_Model_Config_Storage_Locales')
        ),
        'Mage_Core_Model_Config_Modules' => array(
            'parameters' => array('storage' => 'Mage_Core_Model_Config_Storage_Modules')
        ),
        'Mage_Core_Model_Config_Storage' => array(
            'parameters' => array('loader' => 'Mage_Core_Model_Config_Loader_Proxy')
        ),
        'Mage_Core_Model_Config_Storage_Modules' => array(
            'parameters' => array('loader' => 'Mage_Core_Model_Config_Loader_Modules_Proxy')
        ),
        'Mage_Core_Model_Config_Storage_Locales' => array(
            'parameters' => array('loader' => 'Mage_Core_Model_Config_Loader_Locales_Proxy')
        ),
        'Mage_Core_Model_Event_Config' => array(
            'parameters' => array('config' => 'Mage_Core_Model_Config_Modules_Proxy')
        ),
        'Mage_Install_Model_Installer' => array(
            'parameters' => array('config' => 'Mage_Core_Model_Config_Proxy')
        ),
        'Mage_Core_Model_Config_Invalidator' => array(
            'parameters' => array(
                'primaryConfig' => 'Mage_Core_Model_Config_Primary',
                'modulesConfig' => 'Mage_Core_Model_Config_Modules',
                'localesConfig' => 'Mage_Core_Model_Config_Locales',
            )
        ),
        'Magento_Filesystem' => array(
            'parameters' => array(
                'adapter' => 'Magento_Filesystem_Adapter_Local'
            ),
            'shared' => 0
        )
    );

    /**
     * Configure object manager
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function configure(Magento_ObjectManager $objectManager)
    {
        Magento_Profiler::start('initial');
        $objectManager->setConfiguration(array_replace_recursive(
            $this->_initialConfig,
            array(
                'Mage_Core_Model_App_State' => array(
                    array(
                        'parameters' => array(
                            'mode' => $this->_getParam(Mage::PARAM_MODE),
                        )
                    ),
                ),
                'Mage_Core_Model_Dir' => array(
                    'parameters' => array(
                        'baseDir' => $this->_getParam(Mage::PARAM_BASEDIR),
                        'uris' => $this->_getParam(Mage::PARAM_APP_URIS, array()),
                        'dirs' => $this->_getParam(Mage::PARAM_APP_DIRS, array())
                    )
                ),
                'Mage_Core_Model_Config_Loader_Local' => array(
                    'parameters' => array(
                        'customFile' => $this->_getParam(Mage::PARAM_CUSTOM_LOCAL_FILE),
                        'customConfig' => $this->_getParam(Mage::PARAM_CUSTOM_LOCAL_CONFIG)
                    )
                ),
                'Mage_Core_Model_Config_Loader_Modules' => array(
                    'parameters' => array('allowedModules' => $this->_getParam(Mage::PARAM_ALLOWED_MODULES, array()))
                ),
                'Mage_Core_Model_Cache_Frontend_Factory' => array(
                    'parameters' => array(
                        'enforcedOptions' => $this->_getParam(Mage::PARAM_CACHE_OPTIONS, array()),
                        'decorators' => $this->_getCacheFrontendDecorators(),
                    )
                ),
                'Mage_Core_Model_Cache_Types' => array(
                    'parameters' => array(
                        'banAll' => $this->_getParam(Mage::PARAM_BAN_CACHE, false),
                    )
                ),
                'Mage_Core_Model_StoreManager' => array(
                    'parameters' => array(
                        'scopeCode' => $this->_getParam(Mage::PARAM_RUN_CODE, ''),
                        'scopeType' => $this->_getParam(Mage::PARAM_RUN_TYPE, 'store'),
                    )
                )
            )
        ));

        Magento_Profiler::start('primary_load');
        /** @var $config Mage_Core_Model_Config_Primary*/
        $config = $objectManager->get('Mage_Core_Model_Config_Primary');
        Magento_Profiler::stop('primary_load');
        $configurators = $config->getNode('global/configurators');
        if ($configurators) {
            $configurators = $configurators->asArray();
            if (count($configurators)) {
                foreach ($configurators as $configuratorClass) {
                    /** @var $configurator  Magento_ObjectManager_Configuration*/
                    $configurator = $objectManager->create($configuratorClass, array('params' => $this->_params));
                    $configurator->configure($objectManager);
                }
            }
        }
        Magento_Profiler::stop('initial');
        Magento_Profiler::start('global_primary');
        $diConfig = $config->getNode('global/di');
        if ($diConfig) {
            $objectManager->setConfiguration($diConfig->asArray());
        }
        Magento_Profiler::stop('global_primary');
    }

    /**
     * Retrieve cache frontend decorators configuration
     *
     * @return array
     */
    protected function _getCacheFrontendDecorators()
    {
        $result = array();
        // mark all cache entries with a special tag to be able to clean only cache belonging to the application
        $result[] = array(
            'class' => 'Magento_Cache_Frontend_Decorator_TagMarker',
            'parameters' => array('tag' => Mage_Core_Model_AppInterface::CACHE_TAG),
        );
        if (Magento_Profiler::isEnabled()) {
            $result[] = array(
                'class' => 'Magento_Cache_Frontend_Decorator_Profiler',
                'parameters' => array('backendPrefixes' => array('Zend_Cache_Backend_', 'Varien_Cache_Backend_')),
            );
        }
        return $result;
    }
}
