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
        $customPath = null, $cacheOptions = array(), $customLocalXml = null
    ) {
        parent::__construct($baseDir . '/var/di/definitions.php');
        $this->configure(array(
            'Mage_Core_Model_Dir' => array(
                'params' => array('baseDir' => $baseDir, 'customDirs' => $customDirs, 'customPath' => $customPath)
            ),
            'Mage_Core_Model_Config' => array(
                'params' => array('localXml' => $customLocalXml)
            ),
            'Mage_Core_Model_Cache' => array(
                'params' => array('options' => $cacheOptions),
            ),
            'Mage_Core_Model_App' => array(
                'params' => array('scopeCode' => $runCode, 'scopeType' => $runType)
            )
        ));
    }
}
