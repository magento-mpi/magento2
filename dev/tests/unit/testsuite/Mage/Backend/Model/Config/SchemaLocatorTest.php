<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReaderMock;

    /**
     * @var Mage_Backend_Model_Config_SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_moduleReaderMock = $this->getMock('Mage_Core_Model_Config_Modules_Reader', array(), array(), '', false);
        $this->_moduleReaderMock->expects($this->any())
            ->method('getModuleDir')->with('etc', 'Mage_Backend')->will($this->returnValue('schema_dir'));
        $this->_model = new Mage_Backend_Model_Config_SchemaLocator($this->_moduleReaderMock);
    }

    public function testGetSchema()
    {
        $this->assertEquals('schema_dir' . DIRECTORY_SEPARATOR . 'system.xsd', $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals('schema_dir' . DIRECTORY_SEPARATOR . 'system_file.xsd', $this->_model->getPerFileSchema());
    }
}