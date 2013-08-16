<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Modular_SystemConfigFilesTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $objectManager = Mage::getObjectManager();

        // disable config caching to not pollute it
        /** @var $cacheState Mage_Core_Model_Cache_StateInterface */
        $cacheState = $objectManager->get('Mage_Core_Model_Cache_StateInterface');
        $cacheState->setEnabled(Mage_Core_Model_Cache_Type_Config::TYPE_IDENTIFIER, false);

        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = $objectManager->get('Mage_Core_Model_Dir');
        $modulesDir = $dirs->getDir(Mage_Core_Model_Dir::MODULES);

        $fileList = glob($modulesDir . '/*/*/etc/adminhtml/system.xml');

        $configMock = $this->getMock(
            'Mage_Core_Model_Config_Modules_Reader', array('getConfigurationFiles', 'getModuleDir'),
            array(), '', false
        );
        $configMock->expects($this->any())
            ->method('getConfigurationFiles')
            ->will($this->returnValue($fileList))
        ;
        $configMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Mage_Backend')
            ->will($this->returnValue($modulesDir . '/Mage/Backend/etc'))
        ;
        try {
            $objectManager->create('Mage_Backend_Model_Config_Structure_Reader', array(
                'moduleReader' => $configMock,
                'runtimeValidation' => true,
            ));
        } catch (Magento_Exception $exp) {
            $this->fail($exp->getMessage());
        }
    }
}
