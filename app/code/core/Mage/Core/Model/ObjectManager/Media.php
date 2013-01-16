<?php
/**
 * Media downloader application object manager
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager_Media extends Magento_ObjectManager_Zend
{
    /**
     * @param string $basePath
     * @param string $runCode
     * @param string $runType
     * @param bool $disableCacheSave
     * @param array $allowedModules
     */
    public function __construct($basePath, $runCode, $runType, $disableCacheSave, array $allowedModules)
    {
        parent::__construct($basePath . 'var/di/definitions.php');
        $this->configure(array(
            'preference' => array(
                'Mage_Core_Model_Config_StorageInterface' => 'Mage_Core_Model_Config_Storage',
                'Mage_Core_Model_Db_UpdaterInterface' => 'Mage_Core_Model_Db_Updater',
                'Mage_Core_Model_AppInterface' => 'Mage_Core_Model_App_Proxy',
            ),
            'Mage_Core_Model_Dir' => array('parameters' => array('baseDir' => $basePath)),
            'Mage_Core_Model_Cache' => array(
                'parameters' => array('options' => array('disallow_save' => $disableCacheSave))
            ),
            'Mage_Core_Model_Config_Loader_Modules' => array(
                'parameters' => array('allowedModules' => array('Mage_Core'))
            ),
            'Mage_Core_Model_App' => array(
                'parameters' => array('scopeCode' => $runCode, 'scopeType' => $runType)
            ),
        ));
    }
}
