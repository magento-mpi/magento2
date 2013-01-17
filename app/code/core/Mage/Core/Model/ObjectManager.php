<?php
/**
 * Magento Web application object manager. Configures and composes application application to serve http requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager extends Magento_ObjectManager_Zend
{
    /**
     * @param string $baseDir
     * @param string $scopeCode
     * @param $scopeType
     * @param array $customDirs
     * @param array $customUris
     * @param array $allowedModules
     * @param array $cacheOptions
     * @param bool $banCache
     * @param null $customLocalXmlFile
     * @param null $customLocalConfig
     */
    public function __construct(
        $baseDir,
        $scopeCode,
        $scopeType,
        array $customDirs = array(),
        array $customUris = array(),
        array $allowedModules = array(),
        array $cacheOptions = array(),
        $banCache = false,
        $customLocalXmlFile = null,
        $customLocalConfig = null
    ) {
        parent::__construct($baseDir . '/var/di/definitions.php');
        // Instance configuration required for application config loading and configuration based on init options
        $this->configure(array(
            'preference' => array(
                'Mage_Core_Model_Db_UpdaterInterface' => 'Mage_Core_Model_Db_Updater',
                'Mage_Core_Model_AppInterface' => 'Mage_Core_Model_App_Proxy',
            ),
            'Mage_Core_Model_Dir' => array(
                'parameters' => array('baseDir' => $baseDir, 'uris' => $customUris, 'dirs' => $customDirs)
            ),
            'Mage_Core_Model_Config_Loader_Local' => array(
                'parameters' => array('extraFile' => $customLocalXmlFile, 'extraData' => $customLocalConfig)
            ),
            'Mage_Core_Model_Config_Loader_Modules' => array(
                'parameters' => array('allowedModules' => $allowedModules)
            ),
            'Mage_Core_Model_Cache' => array(
                'parameters' => array('options' => $cacheOptions, 'banCache' => $banCache),
            ),
            'Mage_Core_Model_App' => array(
                'parameters' => array('scopeCode' => $scopeCode, 'scopeType' => $scopeType)
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
        ));
        Mage::setObjectManager($this);

        /** @var $config Mage_Core_Model_Config_Primary*/
        $config = $this->get('Mage_Core_Model_Config_Primary');
        $configurators = $config->getNode('global/configurators');
        if ($configurators) {
            $runTypeParams = array(
                'baseDir' => $baseDir,
                'runCode' => $scopeCode,
                'runType' => $scopeType,
                'customDirs' => $customDirs,
                'cacheOptions' => $cacheOptions,
                'customLocalXml' => $customLocalXmlFile,
                'customConfig' => $customLocalConfig,
            );
            foreach ($configurators->children() as $configuratorClass) {
                /** @var $configurator  Magento_ObjectManager_Configuration*/
                $configurator = $this->get((string) $configuratorClass);
                $configurator->configure($this, $runTypeParams);
            }
        }
        $this->loadAreaConfiguration();
    }
}
