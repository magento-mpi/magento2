<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Attribute_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    const FIXTURE_XSD_DIR   = 'fixture_dir';
    const FIXTURE_XSD_FILE  = 'fixture_dir/catalog_attributes.xsd';

    /**
     * @var Magento_Catalog_Model_Attribute_Config_SchemaLocator
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Config_Modules_Reader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReader;

    protected function setUp()
    {
        $this->_moduleReader = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array('getModuleDir'), array(), '', false
        );
        $this->_moduleReader
            ->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Catalog')
            ->will($this->returnValue(self::FIXTURE_XSD_DIR))
        ;
        $this->_model = new Magento_Catalog_Model_Attribute_Config_SchemaLocator($this->_moduleReader);
    }

    public function testGetSchema()
    {
        $this->assertEquals(self::FIXTURE_XSD_FILE, $this->_model->getSchema());
        // Makes sure the value is calculated only once
        $this->assertEquals(self::FIXTURE_XSD_FILE, $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals(self::FIXTURE_XSD_FILE, $this->_model->getPerFileSchema());
        // Makes sure the value is calculated only once
        $this->assertEquals(self::FIXTURE_XSD_FILE, $this->_model->getPerFileSchema());
    }
}
