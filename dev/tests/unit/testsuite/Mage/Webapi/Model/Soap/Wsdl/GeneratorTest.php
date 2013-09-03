<?php
/**
 * Tests for Mage_Webapi_Model_Soap_Wsdl_Generator.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_Wsdl_GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**  @var Mage_Webapi_Model_Soap_Wsdl_Generator */
    protected $_wsdlGenerator;

    /**  @var Mage_Webapi_Model_Soap_Config */
    protected $_soapConfigMock;

    /**  @var Mage_Webapi_Model_Soap_Wsdl_Factory */
    protected $_wsdlFactoryMock;

    /**  @var Mage_Webapi_Helper_Data */
    protected $_helperMock;

    /** @var Mage_Core_Model_CacheInterface */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_soapConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));

        $_wsdlMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl')
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
        $this->_wsdlFactoryMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl_Factory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_wsdlFactoryMock->expects($this->any())->method('create')->will($this->returnValue($_wsdlMock));

        $this->_cacheMock = $this->getMockBuilder('Mage_Core_Model_CacheInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_wsdlGenerator = new Mage_Webapi_Model_Soap_Wsdl_Generator(
            $this->_soapConfigMock,
            $this->_helperMock,
            $this->_wsdlFactoryMock,
            $this->_cacheMock
        );

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_wsdlGenerator);
        unset($this->_soapConfigMock);
        unset($this->_helperMock);
        unset($this->_wsdlFactoryMock);
        unset($this->_cacheMock);
        parent::tearDown();
    }

    public function testGetComplexTypeNodes()
    {
        $serviceName = "serviceName";
        $nodesList = $this->_wsdlGenerator->getComplexTypeNodes($serviceName,
            'ItemsResponse',
            $this->_getXsdDocumentWithReferencedTypes()
        );
        $expectedCount = 2;
        $this->assertCount($expectedCount, $nodesList, "Defined complex types count does not match.");
        $actualTypes = array();
        foreach ($nodesList as $node) {
            $actualTypes[] = $node->getAttribute('name');
        }
        $expectedTypes = array($serviceName . 'ItemsResponse', $serviceName . 'ArrayItem');
        $this->assertEquals(
            $expectedCount,
            count(array_intersect($expectedTypes, $actualTypes)),
            "Complex types does not match."
        );
    }

    /**
     * @return DOMDocument
     */
    protected function _getXsdDocumentWithReferencedTypes()
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
     * @return DOMDocument
     */
    protected function _getXsdDocumentMissingTargetNamespace()
    {
        $xsd =
            '<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
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
            </xsd:schema>';
        $xsdDom = new DOMDocument();
        $xsdDom->loadXML($xsd);
        return $xsdDom;
    }

    /**
     * @dataProvider providerIsComplexType
     */
    public function testIsComplexType($type, $isComplex)
    {
        $this->assertEquals(
            $isComplex,
            $this->_wsdlGenerator->isComplexType($type),
            "Complex type is defined incorrectly"
        );
    }

    public static function providerIsComplexType()
    {
        return array(
            array('xs:int', false),
            array('xsd:string', false),
            array('itemRequest', true),
        );
    }

    /**
     * Test getElementComplexTypeName
     */
    public function testGetElementComplexTypeName()
    {
        $this->assertEquals("Test", $this->_wsdlGenerator->getElementComplexTypeName("test"));
    }

    /**
     * Test getPortTypeName
     */
    public function testGetPortTypeName()
    {
        $this->assertEquals("testPortType", $this->_wsdlGenerator->getPortTypeName("test"));
    }

    /**
     * Test getBindingName
     */
    public function testGetBindingName()
    {
        $this->assertEquals("testBinding", $this->_wsdlGenerator->getBindingName("test"));
    }

    /**
     * Test getPortName
     */
    public function testGetPortName()
    {
        $this->assertEquals("testPort", $this->_wsdlGenerator->getPortName("test"));
    }

    /**
     * test getServiceName
     */
    public function testGetServiceName()
    {
        $this->assertEquals("testService", $this->_wsdlGenerator->getServiceName("test"));
    }

    /**
     * test getOperationName
     */
    public function testGetOperationName()
    {
        $this->assertEquals("resNameMethodName", $this->_wsdlGenerator->getOperationName("resName", "methodName"));
    }

    /**
     * @test
     */
    public function testGetInputMessageName()
    {
        $this->assertEquals("operationNameRequest", $this->_wsdlGenerator->getInputMessageName("operationName"));
    }

    /**
     * @test
     */
    public function testGetOutputMessageName()
    {
        $this->assertEquals("operationNameResponse", $this->_wsdlGenerator->getOutputMessageName("operationName"));
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

        $wsdlGeneratorMock = $this->getMockBuilder(
            'Mage_Webapi_Model_Soap_Wsdl_Generator'
        )
            ->setMethods(array('_prepareServiceData'))
            ->disableOriginalConstructor()
            ->getMock();

        $wsdlGeneratorMock->expects($this->once())->method('_prepareServiceData')->will(
            $this->throwException(new Exception($exceptionMsg))
        );

        $this->assertEquals($genWSDL, $wsdlGeneratorMock->generate($requestedService, 'http://magento.host'));
    }
}
