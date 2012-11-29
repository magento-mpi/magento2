<?php
/**
 * Test Soap server model.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_ServerTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_App */
    protected $_applicationMock;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_requestMock;

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
        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Request_Soap')->disableOriginalConstructor()
            ->getMock();
        $this->_domDocumentFactory = $this->getMockBuilder('Magento_DomDocument_Factory')
            ->disableOriginalConstructor()->getMock();

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_applicationMock);
        unset($this->_requestMock);
        unset($this->_domDocumentFactory);
        unset($this->_storeMock);
        parent::tearDown();
    }

    /**
     * Test Soap server construction with WSDL cache enabling.
     */
    public function testConstructEnableWsdlCache()
    {
        /** Mock getConfig method to return true. */
        $this->_storeMock->expects($this->any())->method('getConfig')->will($this->returnValue(true));
        /** Create Soap server object. */
        $server = new Mage_Webapi_Model_Soap_Server(
            $this->_applicationMock,
            $this->_requestMock,
            $this->_domDocumentFactory
        );
        $server->initWsdlCache();
        /** Assert soap wsdl caching option was enabled after soap server initialization. */
        $this->assertTrue((bool)ini_get('soap.wsdl_cache_enabled'), 'WSDL caching was not enabled.');
    }

    /**
     * Test Soap server construction with WSDL cache disabling.
     */
    public function testConstructDisableWsdlCache()
    {
        /** Mock getConfig method to return false. */
        $this->_storeMock->expects($this->any())->method('getConfig')->will($this->returnValue(false));
        /** Create Soap server object. */
        $server = new Mage_Webapi_Model_Soap_Server(
            $this->_applicationMock,
            $this->_requestMock,
            $this->_domDocumentFactory
        );
        $server->initWsdlCache();
        /** Assert soap wsdl caching option was disabled after soap server initialization. */
        $this->assertFalse((bool)ini_get('soap.wsdl_cache_enabled'), 'WSDL caching was not disabled.');
    }
}
