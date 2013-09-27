<?php
/**
 * Test WSDL generation mechanisms.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_WsdlGenerationTest extends Magento_TestFramework_TestCase_WebapiAbstract
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
            'serviceInterface' => 'Magento_TestModule1_Service_AllSoapAndRestV1Interface',
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
        /** @var Magento_TestFramework_TestCase_Webapi_Adapter_Soap $soapAdapter */
        $soapAdapter = $this->_getWebApiAdapter(self::ADAPTER_SOAP);
        $wsdlUrl = $soapAdapter->generateWsdlUrl(
            array(
                'testModule1AllSoapAndRestV1',
                'testModule1AllSoapAndRestV2',
                'testModule2AllSoapNoRestV1',
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
                "testModule2AllSoapNoRestV1Item",
                (object)array(
                    'id' => 33,
                )
            )
        );
    }

    public function testWsdlGenerationWithNestedTypes()
    {
        $serviceInfo = array(
            'serviceInterface' => 'Magento_TestModule1_Service_AllSoapAndRestV1Interface',
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
        /** @var Magento_TestFramework_TestCase_Webapi_Adapter_Soap $soapAdapter */
        $soapAdapter = $this->_getWebApiAdapter(self::ADAPTER_SOAP);
        $wsdlUrl = $soapAdapter->generateWsdlUrl(array());
        return $wsdlUrl;
    }
}
