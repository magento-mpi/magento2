<?php
/**
 * Test SOAP server model.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_ServerTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Zend\Soap\Server */
    protected $_zendSoapServerMock;

    /** @var Mage_Webapi_Model_Config_Soap */
    protected $_apiConfigMock;

    /** @var Mage_Core_Model_App */
    protected $_applicationMock;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_requestMock;

    /** @var Mage_Webapi_Controller_Dispatcher_Soap_Handler */
    protected $_soapHandler;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    /** @var Mage_Core_Model_Store */
    protected $_storeMock;

    protected function setUp()
    {
        /** Init all dependencies for SUT. */
        $this->_storeMock = $this->getMockBuilder('Mage_Core_Model_Store')->disableOriginalConstructor()->getMock();
        $this->_applicationMock = $this->getMockBuilder('Mage_Core_Model_App')->disableOriginalConstructor()->getMock();
        $this->_applicationMock->expects($this->any())->method('getStore')->will($this->returnValue($this->_storeMock));
        $this->_zendSoapServerMock = $this->getMockBuilder('Zend\Soap\Server')->getMock();
        $this->_zendSoapServerMock->expects($this->any())->method('setWSDL')->will($this->returnSelf());
        $this->_zendSoapServerMock->expects($this->any())->method('setEncoding')->will($this->returnSelf());
        $this->_zendSoapServerMock->expects($this->any())->method('setClassmap')->will($this->returnSelf());
        $this->_zendSoapServerMock->expects($this->any())->method('setReturnResponse')->will($this->returnSelf());
        $this->_zendSoapServerMock->expects($this->any())->method('setObject')->will($this->returnSelf());
        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Request_Soap')->disableOriginalConstructor()
            ->getMock();
        $this->_apiConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Soap')->disableOriginalConstructor()
            ->getMock();
        $this->_soapHandler = $this->getMockBuilder('Mage_Webapi_Controller_Dispatcher_Soap_Handler')
            ->disableOriginalConstructor()->getMock();
        $this->_domDocumentFactory = $this->getMockBuilder('Magento_DomDocument_Factory')
            ->disableOriginalConstructor()->getMock();

        /** Init SUT. */
        $this->_soapServer = new Mage_Webapi_Model_Soap_Server(
            $this->_zendSoapServerMock,
            $this->_apiConfigMock,
            $this->_applicationMock,
            $this->_requestMock,
            $this->_soapHandler,
            $this->_domDocumentFactory
        );

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_soapServer);
        unset($this->_zendSoapServerMock);
        unset($this->_apiConfigMock);
        unset($this->_applicationMock);
        unset($this->_requestMock);
        unset($this->_soapHandler);
        unset($this->_domDocumentFactory);
        unset($this->_storeMock);
        parent::tearDown();
    }

    /**
     * Test Soap server construct.
     */
    public function testConstruct()
    {
        /** Mock required objects for SOAP server init and assert proper methods run and parameter. */
        $this->_storeMock->expects($this->exactly(2))->method('getConfig')->will($this->returnValue(true));
        $this->_applicationMock->expects($this->exactly(3))->method('getStore')->will(
            $this->returnValue($this->_storeMock)
        );
        $this->_zendSoapServerMock->expects($this->once())->method('setReturnResponse')->with($this->equalTo(true));

        new Mage_Webapi_Model_Soap_Server(
            $this->_zendSoapServerMock,
            $this->_apiConfigMock,
            $this->_applicationMock,
            $this->_requestMock,
            $this->_soapHandler,
            $this->_domDocumentFactory
        );
    }

    /**
     * Test Soap server construct with Soap Fault exception.
     */
    public function testConstructSoapFaultException()
    {
        /** Mock objects required for SOAP server init. */
        $this->_storeMock->expects($this->any())->method('getConfig')->will($this->returnValue(true));
        $this->_applicationMock->expects($this->any())->method('getStore')->will(
            $this->returnValue($this->_storeMock)
        );
        $soapFaultException = new SoapFault('400', 'Error', 'Server error', 'Details', 'Fault details', 'Fault header');
        /** Mock Zend Soap Server setWSDL method to throw Soap Fault exception. */
        $this->_zendSoapServerMock->expects($this->once())->method('setWSDL')->will(
            $this->throwException($soapFaultException)
        );
        /** Assert Soap Fault will be re-thrown in initSoapServer method. */
        $this->setExpectedException('SoapFault', 'Error');

        new Mage_Webapi_Model_Soap_Server(
            $this->_zendSoapServerMock,
            $this->_apiConfigMock,
            $this->_applicationMock,
            $this->_requestMock,
            $this->_soapHandler,
            $this->_domDocumentFactory
        );
    }

    /**
     * Test handle method without parameters.
     */
    public function testHandleEmptyParameters()
    {
        /** Mock parent handle method to return passed argument. */
        $this->_zendSoapServerMock->expects($this->once())->method('handle')->will($this->returnArgument(0));
        $this->assertNull($this->_soapServer->handle(), 'Wrong request handling with default parameters.');
    }

    /**
     * Test handle method.
     */
    public function testHandle()
    {
        /** Mock parent handle method to return passed argument. */
        $this->_zendSoapServerMock->expects($this->once())->method('handle')->will($this->returnArgument(0));
        $actualResult = $this->_soapServer->handle('Request');
        $expectedResult = 'Request';
        $this->assertEquals($expectedResult, $actualResult, 'Wrong request handling with parameters.');
    }

    /**
     * Test getApiCharset method.
     */
    public function testGetApiCharset()
    {
        $this->_storeMock->expects($this->once())->method('getConfig')->will($this->returnValue('Windows-1251'));
        $this->assertEquals('Windows-1251', $this->_soapServer->getApiCharset(), 'Wrong API charset encoding getting.');
    }

    /**
     * Test getApiCharset method with default encoding.
     */
    public function testGetApiCharsetDefaultEncoding()
    {
        $this->_storeMock->expects($this->once())->method('getConfig')->will($this->returnValue(null));
        $this->assertEquals(
            Mage_Webapi_Model_Soap_Server::SOAP_DEFAULT_ENCODING,
            $this->_soapServer->getApiCharset(),
            'Wrong default API charset encoding getting.'
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
        $expectedResult = 'http://magento.com/api/soap?resources%5Bres%5D=v1';
        $this->assertEquals($expectedResult, $actualResult, 'Wrong URI generation with default parameter.');
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
        $expectedResult = 'http://magento.com/' . Mage_Webapi_Controller_Router_Route_Webapi::API_AREA_NAME . '/'
            . Mage_Webapi_Controller_Front::API_TYPE_SOAP;
        $actualResult = $this->_soapServer->getEndpointUri();
        $this->assertEquals($expectedResult, $actualResult, 'Wrong endpoint URI building.');
    }

    /**
     * Data provider for generateUri test.
     */
    public function providerForGenerateUriTest()
    {
        return array(
            //Each array contains isWsdl flag, resources, expected URI and assert message.
            'Several resources' => array(
                false,
                array('customer' => 'v1', 'product' => 'v2'),
                'http://magento.com/api/soap?resources%5Bcustomer%5D=v1&resources%5Bproduct%5D=v2',
                'Wrong URI generation with several resources.'
            ),
            'Several resources with WSDL' => array(
                true,
                array('customer' => 'v1', 'product' => 'v2'),
                'http://magento.com/api/soap?resources%5Bcustomer%5D=v1&resources%5Bproduct%5D=v2&wsdl=1',
                'Wrong URI generation with several resources and WSDL.'
            ),
            'Empty resources list' => array(
                true,
                array(),
                'http://magento.com/api/soap?wsdl=1',
                'Wrong URI generation without resources.'
            ),
        );
    }
}
