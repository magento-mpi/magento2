<?php
/**
 * Magento_Core_Model_DataService_Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_DataService_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_DataService_Config
     */
    protected $_dataServiceConfig;

    /** @var Magento_Core_Model_DataService_Config_Reader_Factory */
    private $_readersFactoryMock;

    protected function setUp()
    {
        $reader = $this->getMockBuilder('Magento_Core_Model_DataService_Config_Reader')
            ->disableOriginalConstructor()
            ->getMock();

        $configXml = file_get_contents(__DIR__ . '/_files/service_calls.xml');
        $config = new Magento_Config_Dom($configXml);
        $reader->expects($this->any())
            ->method('getServiceCallConfig')
            ->will($this->returnValue($config->getDom()));

        $this->_readersFactoryMock = $this->getMockBuilder('Magento_Core_Model_DataService_Config_Reader_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_readersFactoryMock->expects($this->any())
            ->method('createReader')
            ->will($this->returnValue($reader));

        /** @var Magento_Core_Model_Config_Modules_Reader $modulesReaderMock */
        $modulesReaderMock = $this->getMockBuilder('Magento_Core_Model_Config_Modules_Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $modulesReaderMock->expects($this->any())
            ->method('getConfigurationFiles')
            ->will($this->returnValue(array()));

        $this->_dataServiceConfig = new Magento_Core_Model_DataService_Config(
            $this->_readersFactoryMock, $modulesReaderMock);
    }

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
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage
     */
    public function testGetClassByAliasInvalidCall()
    {
        $this->_dataServiceConfig->getClassByAlias('missing_service');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage
     */
    public function testGetClassByAliasMethodNotFound()
    {
        $this->_dataServiceConfig->getClassByAlias('missing_retrieval_method');
    }

}
