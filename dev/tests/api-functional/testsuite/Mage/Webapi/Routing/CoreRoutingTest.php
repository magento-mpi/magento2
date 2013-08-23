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
class Mage_Webapi_Routing_CoreRoutingTest extends Magento_Test_TestCase_WebapiAbstract
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
                'httpMethod' => Mage_Webapi_Model_Rest_Config::HTTP_METHOD_GET
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
