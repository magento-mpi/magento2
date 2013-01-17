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
        ),
        'Mage_Core_Model_Config' => array(
            'parameters' => array('storage' => 'Mage_Core_Model_Config_Storage')
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
    );

    public function configure(Magento_ObjectManager $objectManager)
    {
        $objectManager->configure(array_merge(
            $this->_initialConfig,
            array(
                'Mage_Core_Model_Dir' => array(
                    'parameters' => array(
                        'baseDir' => $this->_baseDir, 'uris' => $this->_customUris, 'dirs' => $this->_customDirs
                    )
                ),
                'Mage_Core_Model_Config_Loader_Local' => array(
                    'parameters' => array(
                        'extraFile' => $this->_customLocalXmlFile, 'extraData' => $this->_customLocalConfig
                    )
                ),
                'Mage_Core_Model_Config_Loader_Modules' => array(
                    'parameters' => array('allowedModules' => $this->_allowedModules)
                ),
                'Mage_Core_Model_Cache' => array(
                    'parameters' => array('options' => $this->_cacheOptions, 'banCache' => $this->_banCache),
                ),
                'Mage_Core_Model_App' => array(
                    'parameters' => array('scopeCode' => $this->_scopeCode, 'scopeType' => $this->_scopeType)
                )
            )
        ));

        /** @var $config Mage_Core_Model_Config_Primary*/
        $config = $objectManager->get('Mage_Core_Model_Config_Primary');
        $configurators = $config->getNode('global/configurators');
        if ($configurators) {
            foreach ($configurators->children() as $configuratorClass) {
                $configuratorClass = (string)$configuratorClass;
                /** @var $configurator  Magento_ObjectManager_Configuration*/
                $configurator = new $configuratorClass(
                    $this->_baseDir,
                    $this->_scopeCode,
                    $this->_scopeCode,
                    $this->_customDirs,
                    $this->_customUris,
                    $this->_allowedModules,
                    $this->_cacheOptions,
                    $this->_banCache,
                    $this->_customLocalXmlFile,
                    $this->_customLocalConfig
                );
                $configurator->configure($objectManager);
            }
        }
        $objectManager->loadAreaConfiguration();
    }
}