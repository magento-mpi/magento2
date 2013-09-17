<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test Core Web API routing
 */
class Magento_Webapi_Routing_CoreRoutingTest extends Magento_Test_TestCase_WebapiAbstract
{
    public function testBasicRoutingPathAutoDetection()
    {
        $itemId = 1;
        $serviceInfo = array(
            'serviceInterface' => 'Magento_TestModule1_Service_AllSoapAndRestV1Interface',
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
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET
            ),
            'soap' => array(
                'service' => 'testModule1AllSoapAndRestV1',
                'operation' => 'testModule1AllSoapAndRestV1Item'
            )
        );
        $requestData = array('id' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], "Item was retrieved unsuccessfully");
    }

    public function testExceptionSoapInternalError()
    {
        $this->_markTestAsSoapOnly();
        $serviceInfo = array(
            'serviceInterface' => 'Magento_TestModule3_Service_ErrorV1Interface',
            'method' => 'serviceException',
        );
        $this->setExpectedException(
            'SoapFault',
            'Generic service exception'
        );
        $this->_webApiCall($serviceInfo);
    }
}
