<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Menu_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReaderMock;

    /**
     * @var Magento_Backend_Model_Menu_Config_SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_moduleReaderMock = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array(), array(), '', false
        );
        $this->_moduleReaderMock->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Backend')->will($this->returnValue('schema_dir'));
        $this->_model = new Magento_Backend_Model_Menu_Config_SchemaLocator($this->_moduleReaderMock);
    }

    public function testGetSchema()
    {
        $this->assertEquals('schema_dir' . DIRECTORY_SEPARATOR . 'menu.xsd', $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals(null, $this->_model->getPerFileSchema());
    }
}