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
        'Mage_Core_Model_Config' => array(
            'parameters' => array(
                'storage' => 'Mage_Core_Model_Config_Storage',
            )
        ),
        'Mage_Core_Model_Config_Resource' => array(
            'parameters' => array(
                'config' => 'Mage_Core_Model_Config_Primary',
            )
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
    );

    /**
     * Configure object manager
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function configure(Magento_ObjectManager $objectManager)
    {
        $objectManager->setConfiguration(array_merge(
            $this->_initialConfig,
            array(
                'Mage_Core_Model_Dir' => array(
                    'parameters' => array(
                        'baseDir' => $this->_getParam(Mage::PARAM_BASEDIR),
                        'uris' => $this->_getParam(MAGE::PARAM_APP_URIS, array()),
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
                'Mage_Core_Model_Cache' => array(
                    'parameters' => array(
                        'options' => $this->_getParam(Mage::PARAM_CACHE_OPTIONS, array()),
                        'banCache' => $this->_getParam(Mage::PARAM_BAN_CACHE, false),
                    )
                ),
                'Mage_Core_Model_App' => array(
                    'parameters' => array(
                        'scopeCode' => $this->_getParam(Mage::PARAM_RUN_CODE, ''),
                        'scopeType' => $this->_getParam(Mage::PARAM_RUN_TYPE, 'store')
                    )
                )
            )
        ));

        /** @var $config Mage_Core_Model_Config_Primary*/
        $config = $objectManager->get('Mage_Core_Model_Config_Primary');
        $configurators = $config->getNode('global/configurators')->asArray();
        if (count($configurators)) {
            foreach ($configurators as $configuratorClass) {
                /** @var $configurator  Magento_ObjectManager_Configuration*/
                $configurator = $objectManager->create($configuratorClass, array('params' => $this->_params));
                $configurator->configure($objectManager);
            }
        }
        $objectManager->setConfiguration($config->getNode('global/di')->asArray());
    }
}
