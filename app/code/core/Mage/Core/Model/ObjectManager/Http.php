<?php
/**
 * Magento Web application object manager. Configures and composes application application to serve http requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager_Http extends Magento_ObjectManager_Zend
{
    /**
     * @param string $baseDir
     * @param string$runCode
     * @param string $runType
     * @param array $customDirs
     * @param string $customPath
     * @param array $cacheOptions
     * @param string $customLocalXml
     * @param string $customConfig
     */
    public function __construct(
        $baseDir, $runCode, $runType, $customDirs = null,
        $customPath = null, $cacheOptions = array(), $customLocalXml = null, $customConfig = null
    ) {
        parent::__construct($baseDir . '/var/di/definitions.php');
        $this->configure(array(
            'preference' => array(
                'Mage_Core_Model_Db_UpdaterInterface' => 'Mage_Core_Model_Db_Updater',
                'Mage_Core_Model_AppInterface' => 'Mage_Core_Model_App_Proxy',
            ),
            'Mage_Core_Model_Dir' => array(
                'parameters' => array('baseDir' => $baseDir, 'customDirs' => $customDirs, 'customPath' => $customPath)
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
            'Mage_Core_Model_Config_Loader_Local' => array(
                'parameters' => array('extraFile' => $customLocalXml, 'extraData' => $customConfig)
            ),
            'Mage_Core_Model_Cache' => array(
                'parameters' => array('options' => $cacheOptions),
            ),
            'Mage_Core_Model_App' => array(
                'parameters' => array('scopeCode' => $runCode, 'scopeType' => $runType)
            ),
        ));
        Mage::setObjectManager($this);

        /** @var $config Mage_Core_Model_Config_Primary*/
        $config = $this->get('Mage_Core_Model_Config_Primary');
        $configurators = $config->getNode('global/configurators');
        if ($configurators) {
            $runTypeParams = array(
                'baseDir' => $baseDir,
                'runCode' => $runCode,
                'runType' => $runType,
                'customDirs' => $customDirs,
                'customPath' => $customPath,
                'cacheOptions' => $cacheOptions,
                'customLocalXml' => $customLocalXml,
                'customConfig' => $customConfig,
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
