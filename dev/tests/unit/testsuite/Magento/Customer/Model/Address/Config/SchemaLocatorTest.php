<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Model_Address_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Customer_Model_Address_Config_SchemaLocator
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Config_Modules_Reader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReader;

    /**
     * @var string
     */
    protected $_xsdDir = 'schema_dir';

    /**
     * @var string
     */
    protected $_xsdFile;

    protected function setUp()
    {
        $this->_xsdFile = $this->_xsdDir . '/address_formats.xsd';
        $this->_moduleReader = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array('getModuleDir'), array(), '', false
        );
        $this->_moduleReader
            ->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Customer')
            ->will($this->returnValue($this->_xsdDir))
        ;

        $this->_model = new Magento_Customer_Model_Address_Config_SchemaLocator($this->_moduleReader);
    }

    public function testGetSchema()
    {
        $this->assertEquals($this->_xsdFile, $this->_model->getSchema());
        // Makes sure the value is calculated only once
        $this->assertEquals($this->_xsdFile, $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals($this->_xsdFile, $this->_model->getPerFileSchema());
        // Makes sure the value is calculated only once
        $this->assertEquals($this->_xsdFile, $this->_model->getPerFileSchema());
    }
}
