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
        try {
            $config = Mage::getConfig();
            $cacheMock = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);
            $cacheMock->expects($this->any())->method('canUse')->will($this->returnValue(false));
            $converter = new Mage_Backend_Model_Config_Structure_Converter(
                new Mage_Backend_Model_Config_Structure_Mapper_Factory(Mage::getObjectManager())
            );
            new Mage_Backend_Model_Config_Structure_Reader(
                $config, $cacheMock, $converter, true
            );
        } catch (Magento_Exception $exp) {
            $this->fail($exp->getMessage());
        }
    }
}
