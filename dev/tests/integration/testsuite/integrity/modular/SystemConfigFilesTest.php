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
        $fileList = glob(Mage::getBaseDir('app') . '/*/*/*/*/etc/adminhtml/system.xml');
        try {
            $configMock = $this->getMock(
                'Mage_Core_Model_Config', array('getModuleConfigurationFiles'), array(), '', false
            );
            $configMock->expects($this->any())
                ->method('getModuleConfigurationFiles')
                ->will($this->returnValue($fileList));
            $cacheMock = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);
            $cacheMock->expects($this->any())->method('canUse')->will($this->returnValue(false));
            $converter = new Mage_Backend_Model_Config_Structure_Converter(
                new Mage_Backend_Model_Config_Structure_Mapper_Factory(Mage::getObjectManager())
            );
            new Mage_Backend_Model_Config_Structure_Reader(
                $configMock, $cacheMock, $converter, true
            );
        } catch (Magento_Exception $exp) {
            $this->fail($exp->getMessage());
        }
    }
}
