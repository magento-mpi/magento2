<?php
/**
 * Test SOAP server model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_ServerTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Magento_Core_Model_App */
    protected $_appMock;

    /** @var Magento_Core_Model_Store */
    protected $_storeMock;

    /** @var Magento_Core_Model_Config */
    protected $_configMock;

    /** @var Magento_Webapi_Controller_Soap_Request */
    protected $_requestMock;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    /** @var Mage_Webapi_Controller_Soap_Handler */
    protected $_soapHandler;

    protected function setUp()
    {
        /** Init all dependencies for SUT. */
        $this->_storeMock = $this->getMockBuilder('Magento_Core_Model_Store')->disableOriginalConstructor()->getMock();
        $this->_storeMock->expects($this->any())->method('getBaseUrl')->will(
            $this->returnValue('http://magento.com/')
        );

        $this->_configMock = $this->getMockBuilder('Magento_Core_Model_Config')->disableOriginalConstructor()->getMock();
        $this->_configMock->expects($this->any())->method('getAreaFrontName')->will($this->returnValue('soap'));

        $this->_appMock = $this->getMockBuilder('Magento_Core_Model_App')->disableOriginalConstructor()->getMock();
        $this->_appMock->expects($this->any())->method('getStore')->will($this->returnValue($this->_storeMock));
        $this->_appMock->expects($this->any())->method('getConfig')->will($this->returnValue($this->_configMock));

        $this->_requestMock = $this->getMockBuilder('Magento_Webapi_Controller_Soap_Request')->disableOriginalConstructor()
            ->getMock();

        $this->_domDocumentFactory = $this->getMockBuilder('Magento_DomDocument_Factory')
            ->disableOriginalConstructor()->getMock();

        $this->_soapHandler = $this->getMockBuilder('Mage_Webapi_Controller_Soap_Handler')
            ->disableOriginalConstructor()->getMock();

        /** Init SUT. */
        $this->_soapServer = new Magento_Webapi_Model_Soap_Server(
            $this->_appMock,
            $this->_requestMock,
            $this->_domDocumentFactory,
            $this->_soapHandler
        );

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_soapServer);
        unset($this->_appMock);
        unset($this->_requestMock);
        unset($this->_storeMock);
        unset($this->_soapHandler);
        parent::tearDown();
    }

    /**
     * Test getApiCharset method.
     */
    public function testGetApiCharset()
    {
        $this->_storeMock->expects($this->once())->method('getConfig')->will($this->returnValue('Windows-1251'));
        $this->assertEquals(
            'Windows-1251',
            $this->_soapServer->getApiCharset(),
            'API charset encoding getting is invalid.'
        );
    }

    /**
     * Test getApiCharset method with default encoding.
     */
    public function testGetApiCharsetDefaultEncoding()
    {
        $this->_storeMock->expects($this->once())->method('getConfig')->will($this->returnValue(null));
        $this->assertEquals(
            Magento_Webapi_Model_Soap_Server::SOAP_DEFAULT_ENCODING,
            $this->_soapServer->getApiCharset(),
            'Default API charset encoding getting is invalid.'
        );
    }

    /**
     * Test getEndpointUri method.
     */
    public function testGetEndpointUri()
    {
        $expectedResult = 'http://magento.com/soap';
        $actualResult = $this->_soapServer->getEndpointUri();
        $this->assertEquals($expectedResult, $actualResult, 'Endpoint URI building is invalid.');
    }

    /**
     * Test generate uri with wsdl param as true
     */
    public function testGenerateUriWithWsdlParam()
    {
        $param = "testModule1AllSoapAndRest:V1,testModule2AllSoapNoRest:V1";
        $serviceKey = Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_SERVICES;
        $this->_requestMock->expects($this->any())->method('getParam')
            ->will($this->returnValue($param));
        $expectedResult = "http://magento.com/soap?{$serviceKey}={$param}&wsdl=1";
        $actualResult = $this->_soapServer->generateUri(true);
        $this->assertEquals($expectedResult, urldecode($actualResult), 'URI (with WSDL param) generated is invalid.');
    }

    /**
     * Test generate uri with wsdl param as true
     */
    public function testGenerateUriWithNoWsdlParam()
    {
        $param = "testModule1AllSoapAndRest:V1,testModule2AllSoapNoRest:V1";
        $serviceKey = Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_SERVICES;
        $this->_requestMock->expects($this->any())->method('getParam')
            ->will($this->returnValue($param));
        $expectedResult = "http://magento.com/soap?{$serviceKey}={$param}";
        $actualResult = $this->_soapServer->generateUri(false);
        $this->assertEquals(
            $expectedResult,
            urldecode($actualResult),
            'URI (without WSDL param) generated is invalid.'
        );
    }
}
