<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Modular_SystemConfigFilesTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();

        // disable config caching to not pollute it
        /** @var $cacheState Magento_Core_Model_Cache_StateInterface */
        $cacheState = $objectManager->get('Magento_Core_Model_Cache_StateInterface');
        $cacheState->setEnabled(Magento_Core_Model_Cache_Type_Config::TYPE_IDENTIFIER, false);

        /** @var $dirs Magento_Core_Model_Dir */
        $dirs = $objectManager->get('Magento_Core_Model_Dir');
        $modulesDir = $dirs->getDir(Magento_Core_Model_Dir::MODULES);

        $fileList = glob($modulesDir . '/*/*/etc/adminhtml/system.xml');

        $configMock = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array('getConfigurationFiles', 'getModuleDir'),
            array(), '', false
        );
        $configMock->expects($this->any())
            ->method('getConfigurationFiles')
            ->will($this->returnValue($fileList))
        ;
        $configMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Backend')
            ->will($this->returnValue($modulesDir . '/Magento/Backend/etc'))
        ;
        try {
            $objectManager->create('Magento_Backend_Model_Config_Structure_Reader', array(
                'moduleReader' => $configMock,
                'runtimeValidation' => true,
            ));
        } catch (Magento_Exception $exp) {
            $this->fail($exp->getMessage());
        }
    }
}
