<?php
/**
 * Tests for \Magento\Webapi\Model\Soap\Wsdl\Generator.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap\Wsdl;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**  @var \Magento\Webapi\Model\Soap\Wsdl\Generator */
    protected $_wsdlGenerator;

    /**  @var \Magento\Webapi\Model\Soap\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $_soapConfigMock;

    /**  @var \Magento\Webapi\Model\Soap\Wsdl\Factory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_wsdlFactoryMock;

    /** @var \Magento\Webapi\Model\Cache\Type|\PHPUnit_Framework_MockObject_MockObject */
    protected $_cacheMock;

    /** @var \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor|\PHPUnit_Framework_MockObject_MockObject */
    protected $_typeProcessor;

    protected function setUp()
    {
        $this->_soapConfigMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $_wsdlMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\Wsdl')
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
        $this->_wsdlFactoryMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\Wsdl\Factory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_wsdlFactoryMock->expects($this->any())->method('create')->will($this->returnValue($_wsdlMock));

        $this->_cacheMock = $this->getMockBuilder('Magento\Webapi\Model\Cache\Type')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_cacheMock->expects($this->any())->method('load')->will($this->returnValue(false));
        $this->_cacheMock->expects($this->any())->method('save')->will($this->returnValue(true));

        $this->_typeProcessor = $this->getMock(
            'Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor',
            [],
            [],
            '',
            false
        );

        $this->_wsdlGenerator = new \Magento\Webapi\Model\Soap\Wsdl\Generator(
            $this->_soapConfigMock,
            $this->_wsdlFactoryMock,
            $this->_cacheMock,
            $this->_typeProcessor
        );

        parent::setUp();
    }

    public function testGetComplexTypeNodes()
    {
        /** TODO: Fix */
        $this->markTestIncomplete("Should be fixed after MAGETWO-14491 is done.");
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
     * @return \DOMDocument
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
        $xsdDom = new \DOMDocument();
        $xsdDom->loadXML($xsd);
        return $xsdDom;
    }

    /**
     * @return \DOMDocument
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
        $xsdDom = new \DOMDocument();
        $xsdDom->loadXML($xsd);
        return $xsdDom;
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
     * @expectedException        \Magento\Webapi\Exception
     * @expectedExceptionMessage exception message
     */
    public function testHandleWithException()
    {
        $genWSDL = 'generatedWSDL';
        $exceptionMsg = 'exception message';
        $requestedService = array(
            'catalogProduct',
        );

        $wsdlGeneratorMock = $this->getMockBuilder(
            'Magento\Webapi\Model\Soap\Wsdl\Generator'
        )
            ->setMethods(array('_collectCallInfo'))
            ->setConstructorArgs(
                array(
                    $this->_soapConfigMock,
                    $this->_wsdlFactoryMock,
                    $this->_cacheMock,
                    $this->_typeProcessor
                )
            )
            ->getMock();

        $wsdlGeneratorMock->expects($this->once())->method('_collectCallInfo')->will(
            $this->throwException(new \Magento\Webapi\Exception($exceptionMsg))
        );

        $this->assertEquals($genWSDL, $wsdlGeneratorMock->generate($requestedService, 'http://magento.host'));
    }
}
