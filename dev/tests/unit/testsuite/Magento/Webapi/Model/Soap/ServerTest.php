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
    const WEBAPI_AREA_FRONT_NAME = 'webapi';

    /** @var Magento_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Magento_Core_Model_App */
    protected $_appMock;

    /** @var Magento_Core_Model_Store */
    protected $_storeMock;

    /** @var Magento_Core_Model_Config */
    protected $_configMock;

    /** @var Magento_Webapi_Controller_Request_Soap */
    protected $_requestMock;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    protected function setUp()
    {
        /** Init all dependencies for SUT. */
        $this->_storeMock = $this->getMockBuilder('Magento_Core_Model_Store')->disableOriginalConstructor()->getMock();
        $this->_configMock = $this->getMockBuilder('Magento_Core_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_configMock->expects($this->any())->method('getAreaFrontName')->will(
            $this->returnValue(self::WEBAPI_AREA_FRONT_NAME)
        );
        $this->_appMock = $this->getMockBuilder('Magento_Core_Model_App')->disableOriginalConstructor()->getMock();
        $this->_appMock->expects($this->any())->method('getStore')->will($this->returnValue($this->_storeMock));
        $this->_appMock->expects($this->any())->method('getConfig')->will($this->returnValue($this->_configMock));
        $this->_requestMock = $this->getMockBuilder('Magento_Webapi_Controller_Request_Soap')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_domDocumentFactory = $this->getMockBuilder('Magento_DomDocument_Factory')
            ->disableOriginalConstructor()->getMock();

        /** Init SUT. */
        $this->_soapServer = new Magento_Webapi_Model_Soap_Server(
            $this->_appMock,
            $this->_requestMock,
            $this->_domDocumentFactory
        );

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_soapServer);
        unset($this->_appMock);
        unset($this->_requestMock);
        unset($this->_storeMock);
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
     * Test generateUri method with default parameter.
     */
    public function testGenerateUriDefault()
    {
        $this->_storeMock->expects($this->once())->method('getBaseUrl')->will(
            $this->returnValue('http://magento.com/')
        );
        $this->_requestMock->expects($this->once())->method('getRequestedResources')->will(
            $this->returnValue(array('res' => 'v1'))
        );
        $actualResult = $this->_soapServer->generateUri();
        $expectedResult = 'http://magento.com/' . self::WEBAPI_AREA_FRONT_NAME . '/soap?resources%5Bres%5D=v1';
        $this->assertEquals($expectedResult, $actualResult, 'URI generation with default parameter is invalid.');
    }

    /**
     * Test generateUri method.
     *
     * @dataProvider providerForGenerateUriTest
     */
    public function testGenerateUri($isWsdl, $resources, $expectedUri, $assertMessage)
    {
        $this->_storeMock->expects($this->once())->method('getBaseUrl')->will(
            $this->returnValue('http://magento.com/')
        );
        $this->_requestMock->expects($this->once())->method('getRequestedResources')->will(
            $this->returnValue($resources)
        );
        $actualUri = $this->_soapServer->generateUri($isWsdl);
        $this->assertEquals($expectedUri, $actualUri, $assertMessage);
    }

    /**
     * Test getEndpointUri method.
     */
    public function testGetEndpointUri()
    {
        $this->_storeMock->expects($this->once())->method('getBaseUrl')->will(
            $this->returnValue('http://magento.com/')
        );
        $expectedResult = 'http://magento.com/' . self::WEBAPI_AREA_FRONT_NAME . '/'
            . Magento_Webapi_Controller_Front::API_TYPE_SOAP;
        $actualResult = $this->_soapServer->getEndpointUri();
        $this->assertEquals($expectedResult, $actualResult, 'Endpoint URI building is invalid.');
    }

    /**
     * Test fault method with Exception.
     */
    public function testExceptionFault()
    {
        /** Init Exception. */
        $exception = new Exception();
        $faultResult = $this->_soapServer->fault($exception);
        /** Assert that returned object is instance of SoapFault class. */
        $this->assertInstanceOf('SoapFault', $faultResult, 'SoapFault was not returned.');
    }

    /**
     * Test fault method with Magento_Webapi_Model_Soap_Fault.
     */
    public function testWebapiSoapFault()
    {
        /** Mock Webapi Soap fault. */
        $apiFault = $this->getMockBuilder('Magento_Webapi_Model_Soap_Fault')->disableOriginalConstructor()->getMock();
        /** Assert that mocked fault toXml method will be executed once. */
        $apiFault->expects($this->once())->method('toXml');
        $this->_soapServer->fault($apiFault);
    }

    /**
     * Data provider for generateUri test.
     */
    public function providerForGenerateUriTest()
    {
        $webapiFrontName = self::WEBAPI_AREA_FRONT_NAME;
        return array(
            //Each array contains isWsdl flag, resources, expected URI and assert message.
            'Several resources' => array(
                false,
                array('customer' => 'v1', 'product' => 'v2'),
                "http://magento.com/$webapiFrontName/soap?resources%5Bcustomer%5D=v1&resources%5Bproduct%5D=v2",
                'URI generation with several resources is invalid.'
            ),
            'Several resources with WSDL' => array(
                true,
                array('customer' => 'v1', 'product' => 'v2'),
                "http://magento.com/$webapiFrontName/soap?resources%5Bcustomer%5D=v1&resources%5Bproduct%5D=v2&wsdl=1",
                'URI generation with several resources and WSDL is invalid.'
            ),
            'Empty resources list' => array(
                true,
                array(),
                "http://magento.com/$webapiFrontName/soap?wsdl=1",
                'URI generation without resources is invalid.'
            ),
        );
    }
}
