<?php
/**
 * Test SOAP server model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Model\Soap\Server */
    protected $_soapServer;

    /** @var \Magento\Core\Model\App */
    protected $_appMock;

    /** @var \Magento\Core\Model\Store */
    protected $_storeMock;

    /** @var \Magento\Webapi\Controller\Soap\Request */
    protected $_requestMock;

    /** @var \Magento\DomDocument\Factory */
    protected $_domDocumentFactory;

    /** @var \Magento\Core\Model\StoreManagerInterface */
    protected $_storeManagerMock;

    /** @var \Magento\Webapi\Model\Soap\Server\Factory */
    protected $_soapServerFactory;

    /** @var \Magento\Webapi\Model\Config\ClassReflector\TypeProcessor|\PHPUnit_Framework_MockObject_MockObject */
    protected $_typeProcessor;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMockBuilder(
            'Magento\Core\Model\StoreManager'
        )->disableOriginalConstructor()->getMock();

        $this->_storeMock = $this->getMockBuilder('Magento\Core\Model\Store')->disableOriginalConstructor()->getMock();
        $this->_storeMock->expects(
            $this->any()
        )->method(
            'getBaseUrl'
        )->will(
            $this->returnValue('http://magento.com/')
        );

        $this->_storeManagerMock->expects(
            $this->any()
        )->method(
            'getStore'
        )->will(
            $this->returnValue($this->_storeMock)
        );

        $areaListMock = $this->getMock('Magento\App\AreaList', array(), array(), '', false);
        $configScopeMock = $this->getMock('Magento\Config\ScopeInterface');
        $areaListMock->expects($this->any())->method('getFrontName')->will($this->returnValue('soap'));

        $this->_requestMock = $this->getMockBuilder(
            'Magento\Webapi\Controller\Soap\Request'
        )->disableOriginalConstructor()->getMock();

        $this->_domDocumentFactory = $this->getMockBuilder(
            'Magento\DomDocument\Factory'
        )->disableOriginalConstructor()->getMock();

        $this->_soapServerFactory = $this->getMockBuilder(
            'Magento\Webapi\Model\Soap\Server\Factory'
        )->disableOriginalConstructor()->getMock();

        $this->_typeProcessor = $this->getMock(
            'Magento\Webapi\Model\Config\ClassReflector\TypeProcessor',
            array(),
            array(),
            '',
            false
        );

        /** Init SUT. */
        $this->_soapServer = new \Magento\Webapi\Model\Soap\Server(
            $areaListMock,
            $configScopeMock,
            $this->_requestMock,
            $this->_domDocumentFactory,
            $this->_storeManagerMock,
            $this->_soapServerFactory,
            $this->_typeProcessor
        );

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_soapServer);
        unset($this->_requestMock);
        unset($this->_domDocumentFactory);
        unset($this->_storeMock);
        unset($this->_storeManagerMock);
        unset($this->_soapServerFactory);
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
            \Magento\Webapi\Model\Soap\Server::SOAP_DEFAULT_ENCODING,
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
        $serviceKey = \Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_SERVICES;
        $this->_requestMock->expects($this->any())->method('getParam')->will($this->returnValue($param));
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
        $serviceKey = \Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_SERVICES;
        $this->_requestMock->expects($this->any())->method('getParam')->will($this->returnValue($param));
        $expectedResult = "http://magento.com/soap?{$serviceKey}={$param}";
        $actualResult = $this->_soapServer->generateUri(false);
        $this->assertEquals(
            $expectedResult,
            urldecode($actualResult),
            'URI (without WSDL param) generated is invalid.'
        );
    }
}
