<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesReaderMock;

    /**
     * @var Magento_Core_Model_Resource_Config_SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_modulesReaderMock = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array(), array(), '', false
        );

        $this->_modulesReaderMock->expects($this->once())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Core')
            ->will($this->returnValue('some_path'));

        $this->_model = new Magento_Core_Model_Resource_Config_SchemaLocator($this->_modulesReaderMock);
    }

    /**
     * @covers Magento_Core_Model_Resource_Config_SchemaLocator::getSchema
     */
    public function testGetSchema()
    {
        $expectedSchemaPath = 'some_path' . DIRECTORY_SEPARATOR . 'resources.xsd';
        $this->assertEquals($expectedSchemaPath, $this->_model->getSchema());
    }

    /**
     * @covers Magento_Core_Model_Resource_Config_SchemaLocator::getPerFileSchema
     */
    public function testGetPerFileSchema()
    {
        $this->assertNull($this->_model->getPerFileSchema());
    }
}
