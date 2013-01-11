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
     * @param string $runCode
     * @param string $runType
     * @param array $customDirs
     * @param string $customPath
     * @param array $cacheOptions
     * @param string $customLocalXml
     */
    public function __construct(
        $baseDir, $runCode, $runType, $customDirs = null,
        $customPath = null, $cacheOptions = array(), $customLocalXml = null, $customConfig = null
    ) {
        parent::__construct($baseDir . '/var/di/definitions.php');
        $this->configure(array(
            'preference' => array(
                'Mage_Core_Model_Config_StorageInterface' => 'Mage_Core_Model_Config_Storage',
                'Mage_Core_Model_Db_UpdaterInterface' => 'Mage_Core_Model_Db_Scheme',
            ),
            'Mage_Core_Model_Dir' => array(
                'parameters' => array('baseDir' => $baseDir, 'customDirs' => $customDirs, 'customPath' => $customPath)
            ),
            'Mage_Core_Model_Config_Loader' => array(
                'parameters' => array('loaders' => array(
                        'Mage_Core_Model_Loader_Modules',
                        'Mage_Core_Model_Loader_Db',
                        'Mage_Core_Model_Loader_Locales',
                        'Mage_Core_Model_Loader_Base',
                    )
                )
            ),
            'Mage_Core_Model_Config_Storage' => array(
                'parameters' => array('extraFile' => $customLocalXml, 'extraData' => $customConfig)
            ),
            'Mage_Core_Model_Cache' => array(
                'parameters' => array('options' => $cacheOptions),
            ),
            'Mage_Core_Model_App' => array(
                'parameters' => array('scopeCode' => $runCode, 'scopeType' => $runType)
            ),
            'Magento_Http_Handler_Composite' => array(
                'parameters' => array('handlers' => array('Mage_Core_Model_App'))
            ),
        ));
    }
}
