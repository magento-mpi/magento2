<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Config_Control_QuickStylesTest extends PHPUnit_Framework_TestCase
{
    public function testGetSchemaFile()
    {
        /** @var $moduleReader Mage_Core_Model_Config_Modules_Reader|PHPUnit_Framework_MockObject_MockObject */
        $moduleReader = $this->getMockBuilder('Mage_Core_Model_Config_Modules_Reader')
            ->setMethods(array('getModuleDir'))
            ->disableOriginalConstructor()
            ->getMock();

        $moduleReader->expects($this->any(), $this->any())
            ->method('getModuleDir')
            ->will($this->returnValue('/base_path/etc'));

        /** @var $quickStyle Mage_DesignEditor_Model_Config_Control_QuickStyles */
        $quickStyle = $this->getMock('Mage_DesignEditor_Model_Config_Control_QuickStyles', null, array(
            'moduleReader' => $moduleReader, 'configFiles' => array('sample')
        ), '', false);

        $property = new ReflectionProperty($quickStyle, '_moduleReader');
        $property->setAccessible(true);
        $property->setValue($quickStyle, $moduleReader);

        $this->assertStringMatchesFormat('%s/etc/quick_styles.xsd', $quickStyle->getSchemaFile());
    }
}
