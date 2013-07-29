<?php
use Zend\Soap\Wsdl;

/**
 * Test SOAP Mage_Webapi_Model_Soap_AutoDiscover
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_AutoDiscoverTest extends PHPUnit_Framework_TestCase
{

    /**  @var Mage_Webapi_Model_Soap_AutoDiscover * */
    protected $_autoDiscover;
    /**  @var Mage_Webapi_Config * */
    protected $_newApiConfigMock;
    /**  @var Mage_Webapi_Model_Soap_Wsdl_Factory * */
    protected $_wsdlFactory;
    /**  @var Mage_Webapi_Helper_Config * */
    protected $_helper;
    /**  @var Mage_Core_Model_CacheInterface * */
    protected $_cache;
    /** @var Mage_Webapi_Model_Soap_Wsdl */
    protected $_wsdlMock;


    protected function setUp()
    {

        $this->_newApiConfigMock = $this->getMockBuilder('Mage_Webapi_Config')->disableOriginalConstructor()
            ->getMock();
        $this->_apiConfig = $this->getMockBuilder('Mage_Webapi_Model_Config_Soap')->disableOriginalConstructor()
            ->getMock();
        $this->_wsdlFactory = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl_Factory')->disableOriginalConstructor()
            ->getMock();
        $this->_helper = $this->getMockBuilder('Mage_Webapi_Helper_Config')->disableOriginalConstructor()->getMock();
        $this->_cache = $this->getMockBuilder('Mage_Core_Model_CacheInterface')->disableOriginalConstructor()->getMock(
        );

        $this->_wsdlMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'addSchemaTypeSection',
                    'addService',
                    'addPortType',
                    'addBinding',
                    'addSoapBinding',
                    'addElement',
                    'addComplexType',
                    'addMessage',
                    'addPortOperation',
                    'addBindingOperation',
                    'addSoapOperation',
                    'toXML'
                )
            )
            ->getMock();
        $this->_wsdlFactory = $this->getMock(
            'Mage_Webapi_Model_Soap_Wsdl_Factory',
            array('create'),
            array(new Magento_ObjectManager_ObjectManager())
        );
        $this->_wsdlFactory->expects($this->any())->method('create')->will($this->returnValue($this->_wsdlMock));
        $this->_helper = $this->getMock('Mage_Webapi_Helper_Config', array('__'), array(), '', false, false);
        $this->_helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        //$this->_cacheMock = $this->getMock('Mage_Core_Model_CacheInterface');

        $this->_autoDiscover = new Mage_Webapi_Model_Soap_AutoDiscover(
            $this->_newApiConfigMock,
            $this->_wsdlFactory,
            $this->_helper,
            $this->_cache
        );

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_autoDiscover);
        unset($this->_wsdlFactory);
        unset($this->_helper);
        unset($this->_cache);
        parent::tearDown();
    }

    public function testGetComplexTypeNodes()
    {
        $nodesList = $this->_autoDiscover->getComplexTypeNodes('ItemsResponse', $this->_getXsdDocument());
        $expectedCount = 2;
        $this->assertCount($expectedCount, $nodesList, "Defined complex types count does not match.");
        $actualTypes = array();
        foreach ($nodesList as $node) {
            $actualTypes[] = $node->getAttribute('name');
        }
        $expectedTypes = array('targetNamespaceItemsResponse', 'targetNamespaceArrayItem');
        $this->assertEquals(
            $expectedCount,
            count(array_intersect($expectedTypes, $actualTypes)),
            "Complex types does not match."
        );
    }

    /**
     * @return DOMDocument
     */
    protected function _getXsdDocument()
    {
        $xsd =
            '<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema" targetNamespace="targetNamespace">
                <xsd:complexType name="ItemRequest">
                    <xsd:sequence>
                        <xsd:element name="id" type="xsd:int" />
                    </xsd:sequence>
                </xsd:complexType>
                <xsd:complexType name="ItemResponse">
                    <xsd:sequence>
                        <xsd:element name="id" type="xsd:int" />
                        <xsd:element name="name"  type="xsd:string" />
                    </xsd:sequence>
                </xsd:complexType>

                <xsd:complexType name="ItemsResponse">
                    <xsd:sequence>
                        <xsd:element minOccurs="0" maxOccurs="unbounded" name="complexObjectArray" type="ArrayItem" />
                        <!-- "item" is required to ensure that the same complex type is included into WSDL only once-->
                        <xsd:element name="item" type="ArrayItem" />
                    </xsd:sequence>
                </xsd:complexType>
                <xsd:complexType name="ArrayItem">
                    <xsd:sequence>
                        <xsd:element name="id" type="xsd:int" />
                        <xsd:element name="name" type="xsd:string" />
                    </xsd:sequence>
                </xsd:complexType>
            </xsd:schema>';
        $xsdDom = new DOMDocument();
        $xsdDom->loadXML($xsd);
        return $xsdDom;
    }

    /**
     * Test success case for handle
     */
    public function testHandleSuccess()
    {
        $resData = 'serviceData';
        $genWSDL = 'generatedWSDL';
        $requestedService = array(
            'catalogProduct' => 'V1',
        );

        $partialMockedAutoDis = $this->getMockBuilder(
            'Mage_Webapi_Model_Soap_AutoDiscover'
        )
            ->setMethods(array('_prepareServiceData', 'generate'))
            ->disableOriginalConstructor()
            ->getMock();

        $partialMockedAutoDis->expects($this->once())->method('_prepareServiceData')->will(
            $this->returnValue($resData)
        );
        $partialMockedAutoDis->expects($this->once())->method('generate')->will(
            $this->returnValue($genWSDL)
        );
        $this->assertEquals($genWSDL, $partialMockedAutoDis->handle($requestedService, 'http://magento.host'));
    }

    /**
     * Test exception for handle
     *
     * @expectedException        Mage_Webapi_Exception
     * @expectedExceptionMessage exception message
     */
    public function testHandleWithException()
    {
        $genWSDL = 'generatedWSDL';
        $exceptionMsg = 'exception message';
        $requestedService = array(
            'catalogProduct' => 'V1',
        );

        $partialMockedAutoDis = $this->getMockBuilder(
            'Mage_Webapi_Model_Soap_AutoDiscover'
        )
            ->setMethods(array('_prepareServiceData', 'generate'))
            ->disableOriginalConstructor()
            ->getMock();

        $partialMockedAutoDis->expects($this->once())->method('_prepareServiceData')->will(
            $this->throwException(new Exception($exceptionMsg))
        );

        $this->assertEquals($genWSDL, $partialMockedAutoDis->handle($requestedService, 'http://magento.host'));
    }


    /**
     * Test getElementComplexTypeName
     */
    public function testGetElementComplexTypeName()
    {
        $this->assertEquals("Test", $this->_autoDiscover->getElementComplexTypeName("test"));
    }

    /**
     * Test getPortTypeName
     */
    public function testGetPortTypeName()
    {
        $this->assertEquals("testPortType", $this->_autoDiscover->getPortTypeName("test"));
    }

    /**
     * Test getBindingName
     */
    public function testGetBindingName()
    {
        $this->assertEquals("testBinding", $this->_autoDiscover->getBindingName("test"));
    }

    /**
     * Test getPortName
     */
    public function testGetPortName()
    {
        $this->assertEquals("testPort", $this->_autoDiscover->getPortName("test"));
    }

    /**
     * test getServiceName
     */
    public function testGetServiceName()
    {
        $this->assertEquals("testService", $this->_autoDiscover->getServiceName("test"));
    }

    /**
     * test getOperationName
     */
    public function testGetOperationName()
    {
        $this->assertEquals("resNameMethodName", $this->_autoDiscover->getOperationName("resName", "methodName"));
    }

    /**
     * @test
     */
    public function testGetInputMessageName()
    {
        $this->assertEquals("operationNameRequest", $this->_autoDiscover->getInputMessageName("operationName"));
    }

    /**
     * @test
     */
    public function testGetOutputMessageName()
    {
        $this->assertEquals("operationNameResponse", $this->_autoDiscover->getOutputMessageName("operationName"));
    }

}
