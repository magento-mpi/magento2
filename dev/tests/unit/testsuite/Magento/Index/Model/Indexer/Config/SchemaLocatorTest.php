<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_Indexer_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Index_Model_Indexer_Config_SchemaLocator
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

        $this->_modulesReaderMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Index')
            ->will($this->returnValue('some_path'));

        $this->_model = new Magento_Index_Model_Indexer_Config_SchemaLocator($this->_modulesReaderMock);
    }

    /**
     * @covers Magento_Index_Model_Indexer_Config_SchemaLocator::getSchema
     */
    public function testGetSchema()
    {
        $expectedSchema = 'some_path' . DIRECTORY_SEPARATOR . 'indexers_merged.xsd';
        $this->assertEquals($expectedSchema, $this->_model->getSchema());
    }

    /**
     * @covers Magento_Index_Model_Indexer_Config_SchemaLocator::getPerFileSchema
     */
    public function testGetPerFileSchema()
    {
        $expectedSchema = 'some_path' . DIRECTORY_SEPARATOR . 'indexers.xsd';
        $this->assertEquals($expectedSchema, $this->_model->getPerFileSchema());
    }
}
