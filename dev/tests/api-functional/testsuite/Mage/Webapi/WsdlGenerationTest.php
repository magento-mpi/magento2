<?php
/**
 * Test WSDL generation mechanisms.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_WsdlGenerationTest extends Magento_Test_TestCase_WebapiAbstract
{
    protected function setUp()
    {
        if (TESTS_WEB_API_ADAPTER != self::ADAPTER_SOAP) {
            $this->markTestSkipped("WSDL generation tests are intended to be executed for SOAP adapter only.");
        }
        parent::setUp();
    }

    public function testSingleServiceWsdl()
    {
        $itemId = 1;
        $serviceInfo = array(
                'serviceInterface' => 'Mage_TestModule1_Service_AllSoapAndRestInterfaceV1',
                'method' => 'item'
        );
        $requestData = array('id' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], "WSDL for single resource was generated incorrectly.");
    }

    public function testMultiServiceWsdl()
    {
        /** @var Magento_Test_TestCase_Webapi_Adapter_Soap $soapAdapter */
        $soapAdapter = $this->_getWebApiAdapter(self::ADAPTER_SOAP);
        $wsdlUrl = $soapAdapter->generateWsdlUrl(
            array(
                'testModule1AllSoapAndRest' => 'V2',
                'testModule2AllSoapNoRest' => 'V1',
            )
        );
        $soapClient = $soapAdapter->instantiateSoapClient($wsdlUrl);

        /** Perform action on first service */
        $entityId = 33;
        $soapOperation = "testModule1AllSoapAndRestDelete";
        $actualResponse = $soapClient->$soapOperation(array('id' => $entityId));
        $expectedResponse = (object)array(
            'id' => $entityId,
            'name' => 'testProduct1'
        );
        $this->assertEquals(
            $expectedResponse,
            $actualResponse,
            "Response from '{$soapOperation}' operation is invalid."
        );

        /** Perform action on second service */
        $entityId = 22;
        $soapOperation = "testModule2AllSoapNoRestItem";
        $actualResponse = $soapClient->$soapOperation(array('id' => $entityId));
        $expectedResponse = (object)array(
            'id' => $entityId,
        );
        $this->assertEquals(
            $expectedResponse,
            $actualResponse,
            "Response from '{$soapOperation}' operation is invalid."
        );
    }

    public function testWsdlGenerationWithNestedTypes()
    {
        $serviceInfo = array(
                'serviceInterface' => 'Mage_TestModule1_Service_AllSoapAndRestInterfaceV1',
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
        $this->assertContains("Requested resources are missing.", $responseContent);
    }

    public function testInvalidWsdlUrlInvalidParameter()
    {
        $wsdlUrl = $this->_getBaseWsdlUrl() . '&invalid';
        $responseContent = $this->_getWsdlContent($wsdlUrl);
        $this->assertContains("Not allowed parameters", $responseContent);
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
        $responseDom = new DOMDocument();
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
        /** @var Magento_Test_TestCase_Webapi_Adapter_Soap $soapAdapter */
        $soapAdapter = $this->_getWebApiAdapter(self::ADAPTER_SOAP);
        $wsdlUrl = $soapAdapter->generateWsdlUrl(array());
        return $wsdlUrl;
    }
}
