<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_VersionsCms_Model_Hierarchy_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_VersionsCms_Model_Hierarchy_Config_SchemaLocator
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesReaderMock;

    protected function setUp()
    {
        $this->_modulesReaderMock = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array(), array(), '', false
        );

        $this->_modulesReaderMock->expects($this->once())
            ->method('getModuleDir')
            ->with('etc', 'Magento_VersionsCms')
            ->will($this->returnValue('some_path'));

        $this->_model = new Magento_VersionsCms_Model_Hierarchy_Config_SchemaLocator($this->_modulesReaderMock);
    }

    /**
     * @covers Magento_VersionsCms_Model_Hierarchy_Config_SchemaLocator::getSchema
     */
    public function testGetSchema()
    {
        $expectedSchemaPath = 'some_path' . DIRECTORY_SEPARATOR . 'menu_hierarchy_merged.xsd';
        $this->assertEquals($expectedSchemaPath, $this->_model->getSchema());
    }

    /**
     * @covers Magento_VersionsCms_Model_Hierarchy_Config_SchemaLocator::getPerFileSchema
     */
    public function testGetPerFileSchema()
    {
        $expectedSchemaPath = 'some_path' . DIRECTORY_SEPARATOR . 'menu_hierarchy.xsd';
        $this->assertEquals($expectedSchemaPath, $this->_model->getPerFileSchema());
    }
}
