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

    /** @var Magento_Webapi_Controller_Soap_Request */
    protected $_requestMock;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    /** @var Magento_Core_Model_Store */
    protected $_storeMock;

    /** @var Magento_Webapi_Controller_Soap_Handler */
    protected $_soapHandler;

    /** @var Magento_Core_Model_StoreManagerInterface */
    protected $_storeManagerMock;

    /** @var Magento_Webapi_Model_Soap_Server_Factory */
    protected $_soapServerFactory;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMockBuilder('Magento_Core_Model_StoreManager')
            ->disableOriginalConstructor()->getMock();
        $this->_storeMock = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()->getMock();
        $this->_applicationMock = $this->getMockBuilder('Magento_Core_Model_App')
            ->disableOriginalConstructor()->getMock();
        $this->_storeManagerMock->expects($this->any())
            ->method('getStore')->will($this->returnValue($this->_storeMock));
        $this->_requestMock = $this->getMockBuilder('Magento_Webapi_Controller_Soap_Request')
            ->disableOriginalConstructor()->getMock();
        $this->_domDocumentFactory = $this->getMockBuilder('Magento_DomDocument_Factory')
            ->disableOriginalConstructor()->getMock();
        $this->_soapHandler = $this->getMockBuilder('Magento_Webapi_Controller_Soap_Handler')
            ->disableOriginalConstructor()->getMock();
        $this->_soapServerFactory = $this->getMockBuilder('Magento_Webapi_Model_Soap_Server_Factory')
            ->disableOriginalConstructor()->getMock();

        parent::setUp();
    }

    /**
     * Test SOAP server construction with WSDL cache enabling.
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testConstructEnableWsdlCache()
    {
        /** Mock getConfig method to return true. */
        $this->_storeMock->expects($this->once())->method('getConfig')->will($this->returnValue(true));
        /** Create SOAP server object. */
        $server = new Magento_Webapi_Model_Soap_Server(
            $this->_applicationMock,
            $this->_requestMock,
            $this->_domDocumentFactory,
            $this->_soapHandler,
            $this->_storeManagerMock,
            $this->_soapServerFactory
        );
        /** Assert that SOAP WSDL caching option was enabled after SOAP server initialization. */
        $this->assertTrue((bool)ini_get('soap.wsdl_cache_enabled'), 'WSDL caching was not enabled.');
    }

    /**
     * Test SOAP server construction with WSDL cache disabling.
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testConstructDisableWsdlCache()
    {
        /** Mock getConfig method to return false. */
        $this->_storeMock->expects($this->once())->method('getConfig')->will($this->returnValue(false));
        /** Create SOAP server object. */
        $server = new Magento_Webapi_Model_Soap_Server(
            $this->_applicationMock,
            $this->_requestMock,
            $this->_domDocumentFactory,
            $this->_soapHandler,
            $this->_storeManagerMock,
            $this->_soapServerFactory
        );
        /** Assert that SOAP WSDL caching option was disabled after SOAP server initialization. */
        $this->assertFalse((bool)ini_get('soap.wsdl_cache_enabled'), 'WSDL caching was not disabled.');
    }
}
