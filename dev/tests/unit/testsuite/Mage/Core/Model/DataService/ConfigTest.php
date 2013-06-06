<?php
/**
 * Test class for Mage_Core_Model_DataService_Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_ConfigTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_DataService_Config */
    protected $_dataServiceConfig;

    /** @var  Mage_Core_Model_DataService_Config_Reader */
    private $_reader;

    /**
     * Create Config object to test and mock the reader it is dependant on.
     */
    public function setup()
    {
        $this->_reader = $this->getMockBuilder('Mage_Core_Model_DataService_Config_Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $configXml = file_get_contents(__DIR__ . '/_files/service_calls.xml');
        $config = new Varien_Simplexml_Config($configXml);
        $this->_reader->expects($this->any())
            ->method('getServiceCallConfig')
            ->will($this->returnValue($config));

        $this->_dataServiceConfig = new Mage_Core_Model_DataService_Config($this->_reader);
    }

    /**
     * Make sure the class info for alias is correct
     */
    public function testGetClassByAlias()
    {
        // result should match the config.xml file
        $result = $this->_dataServiceConfig->getClassByAlias('alias');
        $this->assertNotNull($result);
        $this->assertEquals('some_class_name', $result['class']);
        $this->assertEquals('some_method_name', $result['retrieveMethod']);
        $this->assertEquals('foo', $result['methodArguments']['some_arg_name']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Service call with name
     */
    public function testGetClassByAliasNotFound()
    {
        $this->_dataServiceConfig->getClassByAlias('none');
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage
     */
    public function testGetClassByAliasInvalidCall()
    {
        $this->_dataServiceConfig->getClassByAlias('missing_service');
    }

}
