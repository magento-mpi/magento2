<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminGws_Model_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReaderMock;

    /**
     * @var Magento_AdminGws_Model_Config_SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-14185');
        /** @todo Implement test logic here */
        
        $this->_moduleReaderMock = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader',
            array(), array(), '', false
        );
        
        $this->_model = new Magento_AdminGws_Model_Config_SchemaLocator($this->_moduleReaderMock);
    }

    public function testGetSchema()
    {
        $this->markTestIncomplete('MAGETWO-14185');
        /** @todo Implement test logic here */
        
        $this->_model->getSchema();
    }

    public function testGetPerFileSchema()
    {
        $this->markTestIncomplete('MAGETWO-14185');
        /** @todo Implement test logic here */
        
        $this->_model->getPerFileSchema();
    }
}