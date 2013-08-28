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
    /** @var Magento_Core_Model_App */
    protected $_applicationMock;

    /** @var Magento_Webapi_Controller_Request_Soap */
    protected $_requestMock;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    /** @var Magento_Core_Model_Store */
    protected $_storeMock;

    protected function setUp()
    {
        /** Init all dependencies for SUT. */
        $this->_storeMock = $this->getMockBuilder('Magento_Core_Model_Store')->disableOriginalConstructor()->getMock();
        $this->_applicationMock = $this->getMockBuilder('Magento_Core_Model_App')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_applicationMock->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_storeMock));
        $this->_requestMock = $this->getMockBuilder('Magento_Webapi_Controller_Request_Soap')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_domDocumentFactory = $this->getMockBuilder('Magento_DomDocument_Factory')
            ->disableOriginalConstructor()->getMock();

        parent::setUp();
    }

    /**
     * Test SOAP server construction with WSDL cache enabling.
     */
    public function testConstructEnableWsdlCache()
    {
        /** Mock getConfig method to return true. */
        $this->_storeMock->expects($this->any())->method('getConfig')->will($this->returnValue(true));
        /** Create SOAP server object. */
        $server = new Magento_Webapi_Model_Soap_Server(
            $this->_applicationMock,
            $this->_requestMock,
            $this->_domDocumentFactory
        );
        $server->initWsdlCache();
        /** Assert that SOAP WSDL caching option was enabled after SOAP server initialization. */
        $this->assertTrue((bool)ini_get('soap.wsdl_cache_enabled'), 'WSDL caching was not enabled.');
    }

    /**
     * Test SOAP server construction with WSDL cache disabling.
     */
    public function testConstructDisableWsdlCache()
    {
        /** Mock getConfig method to return false. */
        $this->_storeMock->expects($this->any())->method('getConfig')->will($this->returnValue(false));
        /** Create SOAP server object. */
        $server = new Magento_Webapi_Model_Soap_Server(
            $this->_applicationMock,
            $this->_requestMock,
            $this->_domDocumentFactory
        );
        $server->initWsdlCache();
        /** Assert that SOAP WSDL caching option was disabled after SOAP server initialization. */
        $this->assertFalse((bool)ini_get('soap.wsdl_cache_enabled'), 'WSDL caching was not disabled.');
    }
}
