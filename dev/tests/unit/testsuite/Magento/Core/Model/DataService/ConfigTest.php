<?php
/**
 * \Magento\Core\Model\DataService\Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\DataService\Config
     */
    protected $_dataServiceConfig;

    /** @var \Magento\Core\Model\DataService\Config\Reader\Factory */
    private $_readersFactoryMock;

    public function setUp()
    {
        $reader = $this->getMockBuilder('Magento\Core\Model\DataService\Config\Reader')
            ->disableOriginalConstructor()
            ->getMock();

        $configXml = file_get_contents(__DIR__ . '/_files/service_calls.xml');
        $config = new \Magento\Config\Dom($configXml);
        $reader->expects($this->any())
            ->method('getServiceCallConfig')
            ->will($this->returnValue($config->getDom()));

        $this->_readersFactoryMock = $this->getMockBuilder('Magento\Core\Model\DataService\Config\Reader\Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_readersFactoryMock->expects($this->any())
            ->method('createReader')
            ->will($this->returnValue($reader));

        /** @var \Magento\Core\Model\Config\Modules\Reader $modulesReaderMock */
        $modulesReaderMock = $this->getMockBuilder('Magento\Core\Model\Config\Modules\Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $modulesReaderMock->expects($this->any())
            ->method('getConfigurationFiles')
            ->will($this->returnValue(array()));

        $this->_dataServiceConfig = new \Magento\Core\Model\DataService\Config(
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
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Service call with name
     */
    public function testGetClassByAliasNotFound()
    {
        $this->_dataServiceConfig->getClassByAlias('none');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage
     */
    public function testGetClassByAliasInvalidCall()
    {
        $this->_dataServiceConfig->getClassByAlias('missing_service');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage
     */
    public function testGetClassByAliasMethodNotFound()
    {
        $this->_dataServiceConfig->getClassByAlias('missing_retrieval_method');
    }

}
