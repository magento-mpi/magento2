<?php
/**
 * Test WSDL generation mechanisms.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi;

class WsdlGenerationFromDtoTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    protected function setUp()
    {
        $this->_markTestAsSoapOnly("WSDL generation tests are intended to be executed for SOAP adapter only.");
        parent::setUp();
    }

    public function testMultiServiceWsdl()
    {
        $wsdlUrl = $this->_getBaseWsdlUrl() . '&services=testModule4AllSoapAndRestV1,testModule4AllSoapAndRestV2';
        $wsdlContent = $this->_convertXmlToString($this->_getWsdlContent($wsdlUrl));

        $this->_checkTypesDeclaration($wsdlContent);
        $this->_checkPortTypeDeclaration($wsdlContent);
        $this->_checkBindingDeclaration($wsdlContent);
        $this->_checkServiceDeclaration($wsdlContent);
        $this->_checkMessagesDeclaration($wsdlContent);
        $this->_checkFaultsDeclaration($wsdlContent);
    }

    public function testInvalidWsdlUrlNoServices()
    {
        $responseContent = $this->_getWsdlContent($this->_getBaseWsdlUrl());
        /** TODO: Change current assert and add new ones when behavior is changed */
        $this->assertContains("Requested services are missing.", $responseContent);
    }

    public function testInvalidWsdlUrlInvalidParameter()
    {
        $wsdlUrl = $this->_getBaseWsdlUrl() . '&invalid';
        $responseContent = $this->_getWsdlContent($wsdlUrl);
        $this->assertContains("Not allowed parameters", $responseContent);
    }

    /**
     * Remove unnecessary spaces and line breaks from xml string.
     *
     * @param string $xml
     * @return string
     */
    protected function _convertXmlToString($xml)
    {
        return str_replace(array('    ', "\n", "\r"), '', $xml);
    }

    /**
     * Retrieve WSDL content.
     *
     * @param string $wsdlUrl
     * @return string|boolean
     */
    protected function _getWsdlContent($wsdlUrl)
    {
        $connection = curl_init($wsdlUrl);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        $responseContent = curl_exec($connection);
        $responseDom = new \DOMDocument();
        $this->assertTrue(
            $responseDom->loadXML($responseContent),
            "Valid XML is always expected as a response for WSDL request."
        );
        return $responseContent;
    }

    /**
     * Generate base WSDL URL (without any services specified)
     *
     * @return string
     */
    protected function _getBaseWsdlUrl()
    {
        /** @var \Magento\TestFramework\TestCase\Webapi\Adapter\Soap $soapAdapter */
        $soapAdapter = $this->_getWebApiAdapter(self::ADAPTER_SOAP);
        $wsdlUrl = $soapAdapter->generateWsdlUrl(array());
        return $wsdlUrl;
    }

    /**
     * Ensure that types section has correct structure.
     *
     * @param string $wsdlContent
     */
    protected function _checkTypesDeclaration($wsdlContent)
    {
        $baseUrl = TESTS_BASE_URL;
        // @codingStandardsIgnoreStart
        $typesSectionDeclaration = <<< TYPES_SECTION_DECLARATION
<types>
    <xsd:schema targetNamespace="{$baseUrl}/soap?services%3DtestModule4AllSoapAndRestV1%2CtestModule4AllSoapAndRestV2">
TYPES_SECTION_DECLARATION;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($typesSectionDeclaration),
            $wsdlContent,
            'Types section declaration is invalid'
        );
        $this->_checkElementsDeclaration($wsdlContent);
        $this->_checkComplexTypesDeclaration($wsdlContent);
    }

    /**
     * @param string $wsdlContent
     */
    protected function _checkElementsDeclaration($wsdlContent)
    {
        // @codingStandardsIgnoreStart
        $elements = <<< ELEMENTS
<xsd:element name="testModule4AllSoapAndRestV1ItemRequest" type="tns:testModule4AllSoapAndRestV1ItemRequest"/>
<xsd:element name="testModule4AllSoapAndRestV1ItemsRequest" type="tns:testModule4AllSoapAndRestV1ItemsRequest"/>
<xsd:element name="testModule4AllSoapAndRestV1CreateRequest" type="tns:testModule4AllSoapAndRestV1CreateRequest"/>
<xsd:element name="testModule4AllSoapAndRestV1UpdateRequest" type="tns:testModule4AllSoapAndRestV1UpdateRequest"/>
<xsd:element name="testModule4AllSoapAndRestV2ItemRequest" type="tns:testModule4AllSoapAndRestV2ItemRequest"/>
<xsd:element name="testModule4AllSoapAndRestV2ItemsRequest" type="tns:testModule4AllSoapAndRestV2ItemsRequest"/>
<xsd:element name="testModule4AllSoapAndRestV2CreateRequest" type="tns:testModule4AllSoapAndRestV2CreateRequest"/>
<xsd:element name="testModule4AllSoapAndRestV2UpdateRequest" type="tns:testModule4AllSoapAndRestV2UpdateRequest"/>
<xsd:element name="testModule4AllSoapAndRestV2DeleteRequest" type="tns:testModule4AllSoapAndRestV2DeleteRequest"/>
ELEMENTS;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($elements),
            $wsdlContent,
            'Elements declaration in types section is invalid'
        );
    }

    /**
     * @param string $wsdlContent
     */
    protected function _checkComplexTypesDeclaration($wsdlContent)
    {
        // TODO: Implement
    }

    /**
     * Ensure that port type sections have correct structure.
     *
     * @param string $wsdlContent
     */
    protected function _checkPortTypeDeclaration($wsdlContent)
    {
        // @codingStandardsIgnoreStart
        $firstPortType = <<< FIRST_PORT_TYPE
<portType name="testModule4AllSoapAndRestV1PortType">
FIRST_PORT_TYPE;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($firstPortType),
            $wsdlContent,
            'Port type declaration is missing or invalid'
        );
        // @codingStandardsIgnoreStart
        $secondPortType = <<< SECOND_PORT_TYPE
<portType name="testModule4AllSoapAndRestV2PortType">
SECOND_PORT_TYPE;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($secondPortType),
            $wsdlContent,
            'Port type declaration is missing or invalid'
        );
        // @codingStandardsIgnoreStart
        $operationDeclaration = <<< OPERATION_DECLARATION
<operation name="testModule4AllSoapAndRestV2Item">
    <input message="tns:testModule4AllSoapAndRestV2ItemRequest"/>
    <fault name="DefaultFault" message="tns:DefaultFault"/>
</operation>
<operation name="testModule4AllSoapAndRestV2Items">
    <input message="tns:testModule4AllSoapAndRestV2ItemsRequest"/>
    <fault name="DefaultFault" message="tns:DefaultFault"/>
</operation>
OPERATION_DECLARATION;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($operationDeclaration),
            $wsdlContent,
            'Operation in port type is invalid'
        );
    }

    /**
     * Ensure that binding sections have correct structure.
     *
     * @param string $wsdlContent
     */
    protected function _checkBindingDeclaration($wsdlContent)
    {
        // @codingStandardsIgnoreStart
        $firstBinding = <<< FIRST_BINDING
<binding name="testModule4AllSoapAndRestV1Binding" type="tns:testModule4AllSoapAndRestV1PortType">
    <soap12:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>
FIRST_BINDING;
        // @codingStandardsIgnoreEndd
        $this->assertContains(
            $this->_convertXmlToString($firstBinding),
            $wsdlContent,
            'Binding declaration is missing or invalid'
        );
        // @codingStandardsIgnoreStart
        $secondBinding = <<< SECOND_BINDING
<binding name="testModule4AllSoapAndRestV2Binding" type="tns:testModule4AllSoapAndRestV2PortType">
    <soap12:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>
SECOND_BINDING;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($secondBinding),
            $wsdlContent,
            'Binding declaration is missing or invalid'
        );
        // @codingStandardsIgnoreStart
        $operationDeclaration = <<< OPERATION_DECLARATION
<operation name="testModule4AllSoapAndRestV1Item">
    <soap:operation soapAction="testModule4AllSoapAndRestV1Item"/>
    <input>
        <soap12:body use="literal"/>
    </input>
    <fault name="DefaultFault">
        <soap:fault name="DefaultFault" use="literal"/>
    </fault>
</operation>
<operation name="testModule4AllSoapAndRestV1Items">
    <soap:operation soapAction="testModule4AllSoapAndRestV1Items"/>
    <input>
        <soap12:body use="literal"/>
    </input>
    <fault name="DefaultFault">
        <soap:fault name="DefaultFault" use="literal"/>
    </fault>
</operation>
OPERATION_DECLARATION;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($operationDeclaration),
            $wsdlContent,
            'Operation in binding is invalid'
        );
    }

    /**
     * Ensure that service sections have correct structure.
     *
     * @param string $wsdlContent
     */
    protected function _checkServiceDeclaration($wsdlContent)
    {
        $baseUrl = TESTS_BASE_URL;
        // @codingStandardsIgnoreStart
        $firstServiceDeclaration = <<< FIRST_SERVICE_DECLARATION
<service name="testModule4AllSoapAndRestV1Service">
    <port name="testModule4AllSoapAndRestV1Port" binding="tns:testModule4AllSoapAndRestV1Binding">
        <soap:address location="{$baseUrl}/soap?services=testModule4AllSoapAndRestV1%2CtestModule4AllSoapAndRestV2"/>
    </port>
</service>
FIRST_SERVICE_DECLARATION;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($firstServiceDeclaration),
            $wsdlContent,
            'Service section is invalid'
        );

        // @codingStandardsIgnoreStart
        $secondServiceDeclaration = <<< SECOND_SERVICE_DECLARATION
<service name="testModule4AllSoapAndRestV2Service">
    <port name="testModule4AllSoapAndRestV2Port" binding="tns:testModule4AllSoapAndRestV2Binding">
        <soap:address location="{$baseUrl}/soap?services=testModule4AllSoapAndRestV1%2CtestModule4AllSoapAndRestV2"/>
    </port>
</service>
SECOND_SERVICE_DECLARATION;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($secondServiceDeclaration),
            $wsdlContent,
            'Service section is invalid'
        );
    }

    /**
     * Ensure that messages sections have correct structure.
     *
     * @param string $wsdlContent
     */
    protected function _checkMessagesDeclaration($wsdlContent)
    {
        // @codingStandardsIgnoreStart
        $messagesDeclaration = <<< MESSAGES_DECLARATION
<message name="testModule4AllSoapAndRestV2ItemRequest">
    <part name="messageParameters" element="tns:testModule4AllSoapAndRestV2ItemRequest"/>
</message>
<message name="testModule4AllSoapAndRestV2ItemsRequest">
    <part name="messageParameters" element="tns:testModule4AllSoapAndRestV2ItemsRequest"/>
</message>
MESSAGES_DECLARATION;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($messagesDeclaration),
            $wsdlContent,
            'Service section is invalid'
        );
    }

    /**
     * Ensure that SOAP faults are declared properly.
     *
     * @param string $wsdlContent
     */
    protected function _checkFaultsDeclaration($wsdlContent)
    {
        $this->_checkFaultsPortTypeSection($wsdlContent);
        $this->_checkFaultsBindingSection($wsdlContent);
        $this->_checkFaultsMessagesSection($wsdlContent);
        $this->_checkFaultsComplexTypeSection($wsdlContent);
    }

    /**
     * @param string $wsdlContent
     */
    protected function _checkFaultsPortTypeSection($wsdlContent)
    {
        // @codingStandardsIgnoreStart
        $faultsInPortType = <<< FAULT_IN_PORT_TYPE
<fault name="DefaultFault" message="tns:DefaultFault"/>
FAULT_IN_PORT_TYPE;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($faultsInPortType),
            $wsdlContent,
            'SOAP Fault section in port type section is invalid'
        );
    }

    /**
     * @param string $wsdlContent
     */
    protected function _checkFaultsBindingSection($wsdlContent)
    {
        $faultsInBinding = <<< FAULT_IN_BINDING
<fault name="DefaultFault">
    <soap:fault name="DefaultFault" use="literal"/>
</fault>
FAULT_IN_BINDING;
        $this->assertContains(
            $this->_convertXmlToString($faultsInBinding),
            $wsdlContent,
            'SOAP Fault section in binding section is invalid'
        );
    }

    /**
     * @param string $wsdlContent
     */
    protected function _checkFaultsMessagesSection($wsdlContent)
    {
        // TODO: Uncomment after implementation
        return;
        $defaultFaultMessage = <<< DEFAULT_FAULT_IN_MESSAGES
<message name="DefaultFault">
    <part name="messageParameters" element="tns:DefaultFault"/>
</message>
DEFAULT_FAULT_IN_MESSAGES;
        $this->assertContains(
            $this->_convertXmlToString($defaultFaultMessage),
            $wsdlContent,
            'Default SOAP Fault declaration in messages section is invalid'
        );

        $faultsInMessages = <<< FAULT_IN_MESSAGES
<message name="testModule3ErrorV1ParameterizedServiceExceptionFirstFault">
    <part name="messageParameters" element="tns:testModule3ErrorV1ParameterizedServiceExceptionFirstFault"/>
</message>
<message name="testModule3ErrorV1ParameterizedServiceExceptionSecondFault">
    <part name="messageParameters" element="tns:testModule3ErrorV1ParameterizedServiceExceptionSecondFault"/>
</message>
FAULT_IN_MESSAGES;
        $this->assertContains(
            $this->_convertXmlToString($faultsInMessages),
            $wsdlContent,
            'SOAP Fault declaration in messages section is invalid'
        );
    }

    /**
     * @param string $wsdlContent
     */
    protected function _checkFaultsComplexTypeSection($wsdlContent)
    {
        // TODO: Uncomment after implementation
        return;
        $firstFaultType = <<< FIRST_FAULT_IN_COMPLEX_TYPES
<xsd:complexType name="testModule3ErrorV1ParameterizedServiceExceptionFirstFault">
    <xsd:sequence>
        <xsd:element name="Code" type="xsd:int"/>
        <xsd:element name="Trace" type="xsd:string" minOccurs="0"/>
        <xsd:element name="Parameters" type="tns:testModule3ErrorV1ParameterizedServiceExceptionFirstFaultParams"/>
    </xsd:sequence>
</xsd:complexType>
<xsd:complexType name="testModule3ErrorV1ParameterizedServiceExceptionFirstFaultParams">
    <xsd:sequence>
        <xsd:element name="firstFaultMessage" type="xsd:string"/>
        <xsd:element name="firstFaultDetail1" type="xsd:double"/>
        <xsd:element name="firstFaultDetail2" type="xsd:int"/>
    </xsd:sequence>
</xsd:complexType>
FIRST_FAULT_IN_COMPLEX_TYPES;

        $this->assertContains(
            $this->_convertXmlToString($firstFaultType),
            $wsdlContent,
            'First SOAP Fault complex types declaration is invalid'
        );

        $firstElement = '<xsd:element name="testModule3ErrorV1ParameterizedServiceExceptionFirstFault" '
            . 'type="tns:testModule3ErrorV1ParameterizedServiceExceptionFirstFault"/>';
        $this->assertContains(
            $this->_convertXmlToString($firstElement),
            $wsdlContent,
            'First SOAP Fault complex type element declaration is invalid'
        );

        $secondFaultType = <<< SECOND_FAULT_IN_COMPLEX_TYPES
<xsd:complexType name="testModule3ErrorV1ParameterizedServiceExceptionSecondFault">
    <xsd:sequence>
        <xsd:element name="Code" type="xsd:int"/>
        <xsd:element name="Trace" type="xsd:string" minOccurs="0"/>
        <xsd:element name="Parameters" type="tns:testModule3ErrorV1ParameterizedServiceExceptionSecondFaultParams"/>
    </xsd:sequence>
</xsd:complexType>
<xsd:complexType name="testModule3ErrorV1ParameterizedServiceExceptionSecondFaultParams">
    <xsd:sequence>
        <xsd:element name="secondFaultMessage" type="xsd:string"/>
        <xsd:element name="secondFaultDetail1" type="xsd:double"/>
    </xsd:sequence>
</xsd:complexType>
SECOND_FAULT_IN_COMPLEX_TYPES;
        $this->assertContains(
            $this->_convertXmlToString($secondFaultType),
            $wsdlContent,
            'Second SOAP Fault complex types declaration is invalid'
        );

        $secondElement = '<xsd:element name="testModule3ErrorV1ParameterizedServiceExceptionSecondFault" '
            . 'type="tns:testModule3ErrorV1ParameterizedServiceExceptionSecondFault"/>';
        $this->assertContains(
            $this->_convertXmlToString($secondElement),
            $wsdlContent,
            'Second SOAP Fault complex type element declaration is invalid'
        );

        $defaultFaultType = <<< DEFAULT_FAULT_COMPLEX_TYPE
<xsd:complexType name="DefaultFault">
    <xsd:sequence>
        <xsd:element name="Code" type="xsd:int"/>
        <xsd:element name="Trace" type="xsd:string" minOccurs="0"/>
    </xsd:sequence>
</xsd:complexType>
DEFAULT_FAULT_COMPLEX_TYPE;
        $this->assertContains(
            $this->_convertXmlToString($defaultFaultType),
            $wsdlContent,
            'Default SOAP Fault complex types declaration is invalid'
        );

        $defaultElement = '<xsd:element name="DefaultFault" type="tns:DefaultFault"/>';
        $this->assertContains(
            $this->_convertXmlToString($defaultElement),
            $wsdlContent,
            'Default SOAP Fault complex type element declaration is invalid'
        );

    }
}
