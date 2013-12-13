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

class WsdlGenerationTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    protected function setUp()
    {
        $this->_markTestAsSoapOnly("WSDL generation tests are intended to be executed for SOAP adapter only.");
        parent::setUp();
    }

    public function testSingleServiceWsdl()
    {
        $itemId = 1;
        $serviceInfo = array(
            'serviceInterface' => 'Magento\TestModule1\Service\AllSoapAndRestV1Interface',
            'method' => 'item'
        );
        $requestData = array('id' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], "WSDL for single resource was generated incorrectly.");
    }

    /**
     * @dataProvider providerTestMultiServiceWsdl
     * @param $entityId
     * @param $soapOperation
     * @param $expectedResponse
     */
    public function testMultiServiceWsdl($entityId, $soapOperation, $expectedResponse)
    {
        /** @var \Magento\TestFramework\TestCase\Webapi\Adapter\Soap $soapAdapter */
        $soapAdapter = $this->_getWebApiAdapter(self::ADAPTER_SOAP);
        $wsdlUrl = $soapAdapter->generateWsdlUrl(
            array(
                'testModule1AllSoapAndRestV1',
                'testModule1AllSoapAndRestV2',
                'testModule2SubsetRestV1',
            )
        );
        $soapClient = $soapAdapter->instantiateSoapClient($wsdlUrl);

        $actualResponse = $soapClient->$soapOperation(array('id' => $entityId));
        $this->assertEquals(
            $expectedResponse,
            $actualResponse,
            "Response from '{$soapOperation}' operation is invalid."
        );
    }

    public function providerTestMultiServiceWsdl()
    {
        return array(
            array(
                11,
                "testModule1AllSoapAndRestV1Item",
                (object)array(
                    'id' => 11,
                    'name' => 'testProduct1'
                )
            ),
            array(
                22,
                "testModule1AllSoapAndRestV2Item",
                (object)array(
                    'id' => 22,
                    'name' => 'testProduct1',
                    'price' => '1'
                )
            ),
            array(
                33,
                "testModule2SubsetRestV1Item",
                (object)array(
                    'id' => 33,
                )
            )
        );
    }

    public function testWsdlGenerationWithNestedTypes()
    {
        $serviceInfo = array(
            'serviceInterface' => 'Magento\TestModule1\Service\AllSoapAndRestV1Interface',
            'method' => 'items'
        );
        $actualResult = $this->_webApiCall($serviceInfo);
        $expectedResult = array(
            array(
                'id' => 1,
                'name' => 'testProduct1'
            ),
            array(
                'id' => 2,
                'name' => 'testProduct2'
            )
        );
        $this->assertEquals(
            $expectedResult,
            $actualResult,
            "WSDL with nested complex types was generated incorrectly."
        );
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

    public function testSoapFaultBinding()
    {
        $wsdlUrl = $this->_getBaseWsdlUrl() . '&services=testModule3ErrorV1';
        $wsdlContent = $this->_convertXmlToString($this->_getWsdlContent($wsdlUrl));

        $this->_checkFaultsPortTypeSection($wsdlContent);
        $this->_checkFaultsBindingSection($wsdlContent);
        $this->_checkFaultsMessagesSection($wsdlContent);
        $this->_checkFaultsComplexTypeSection($wsdlContent);
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
     * @param $wsdlContent
     */
    protected function _checkFaultsPortTypeSection($wsdlContent)
    {
        // @codingStandardsIgnoreStart
        $faultsInPortType = <<< FAULT_IN_PORT_TYPE
<operation name="testModule3ErrorV1ParameterizedServiceException">
    <input message="tns:testModule3ErrorV1ParameterizedServiceExceptionRequest"/>
    <output message="tns:testModule3ErrorV1ParameterizedServiceExceptionResponse"/>
    <fault name="DefaultFault" message="tns:DefaultFault"/>
    <fault name="testModule3ErrorV1ParameterizedServiceExceptionFirst" message="tns:testModule3ErrorV1ParameterizedServiceExceptionFirstFault"/>
    <fault name="testModule3ErrorV1ParameterizedServiceExceptionSecond" message="tns:testModule3ErrorV1ParameterizedServiceExceptionSecondFault"/>
</operation>
FAULT_IN_PORT_TYPE;
        // @codingStandardsIgnoreEnd
        $this->assertContains(
            $this->_convertXmlToString($faultsInPortType),
            $wsdlContent,
            'SOAP Fault section in port type section is invalid'
        );
    }

    /**
     * @param $wsdlContent
     */
    protected function _checkFaultsBindingSection($wsdlContent)
    {
        $faultsInBinding = <<< FAULT_IN_BINDING
<fault name="DefaultFault">
    <soap:fault name="DefaultFault" use="literal"/>
</fault>
<fault name="testModule3ErrorV1ParameterizedServiceExceptionFirst">
    <soap:fault name="testModule3ErrorV1ParameterizedServiceExceptionFirst" use="literal"/>
</fault>
<fault name="testModule3ErrorV1ParameterizedServiceExceptionSecond">
    <soap:fault name="testModule3ErrorV1ParameterizedServiceExceptionSecond" use="literal"/>
</fault>
FAULT_IN_BINDING;
        $this->assertContains(
            $this->_convertXmlToString($faultsInBinding),
            $wsdlContent,
            'SOAP Fault section in binding section is invalid'
        );
    }

    /**
     * @param $wsdlContent
     */
    protected function _checkFaultsMessagesSection($wsdlContent)
    {
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
    }

    /**
     * @param $wsdlContent
     */
    protected function _checkFaultsComplexTypeSection($wsdlContent)
    {
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
