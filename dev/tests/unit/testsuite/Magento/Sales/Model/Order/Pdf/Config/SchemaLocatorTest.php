<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Order_Pdf_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Model_Order_Pdf_Config_SchemaLocator
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

    protected function setUp()
    {
        $this->_moduleReader = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array('getModuleDir'), array(), '', false
        );
        $this->_moduleReader
            ->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Sales')
            ->will($this->returnValue($this->_xsdDir))
        ;

        $this->_model = new Magento_Sales_Model_Order_Pdf_Config_SchemaLocator($this->_moduleReader);
    }

    public function testGetSchema()
    {
        $file = $this->_xsdDir . '/pdf.xsd';
        $this->assertEquals($file, $this->_model->getSchema());
        // Make sure the value is calculated only once
        $this->assertEquals($file, $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $file = $this->_xsdDir . '/pdf_file.xsd';
        $this->assertEquals($file, $this->_model->getPerFileSchema());
        // Make sure the value is calculated only once
        $this->assertEquals($file, $this->_model->getPerFileSchema());
    }
}
