<?php
/**
 * Test Web API routing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_RoutingTest extends Magento_Test_TestCase_WebapiAbstract
{
    public function testBasicRoutingPathAutoDetection()
    {
        $itemId = 1;
        $serviceInfo = array(
            'serviceInterface' => 'Mage_TestModule1_Service_AllSoapAndRestInterfaceV1',
            'method' => 'item',
            'entityId' => $itemId
        );
        $requestData = array('id' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], "Item was retrieved unsuccessfully");
    }

    public function testBasicRoutingExplicitPath()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/testmodule1/' . $itemId,
                'httpMethod' => 'GET'
            ),
            'soap' => array(
                'service' => 'testModule1AllSoapAndRest',
                'serviceVersion' => 'V1',
                'operation' => 'testModule1AllSoapAndRestItem'
            )
        );
        $requestData = array('id' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], "Item was retrieved unsuccessfully");
    }

    public function testExceptionSoapMissingRequiredField()
    {
        $this->_markTestAsSoapOnly();
        $serviceInfo = array(
            'serviceInterface' => 'Mage_TestModule1_Service_AllSoapAndRestInterfaceV1',
            'method' => 'item',
        );
        $this->setExpectedException(
            'SoapFault',
            "Encoding: object has no 'id' property"
        );
        /** Params are intentionally omitted to cause exception */
        $this->_webApiCall($serviceInfo);
    }

    public function testExceptionSoapInvalidOperation()
    {
        $this->_markTestAsSoapOnly();
        $serviceInfo = array(
            'serviceInterface' => 'Mage_TestModule1_Service_AllSoapAndRestInterfaceV1',
            'method' => 'invalid',
        );
        $this->setExpectedException(
            'SoapFault',
            'Function ("testModule1AllSoapAndRestInvalid") is not a valid method for this service'
        );
        $this->_webApiCall($serviceInfo);
    }

    public function testExceptionSoapInternalError()
    {
        // TODO: Uncomment test
        $this->markTestIncomplete("Uncomment test when TestModule3 WSDL is generated correctly");
        $this->_markTestAsSoapOnly();
        $serviceInfo = array(
            'serviceInterface' => 'Mage_TestModule3_Service_ErrorInterfaceV1',
            'method' => 'serviceException',
        );
        $this->setExpectedException(
            'SoapFault',
            'Internal Error. Details are available in Magento log file.'
        );
        $this->_webApiCall($serviceInfo);
    }
}
