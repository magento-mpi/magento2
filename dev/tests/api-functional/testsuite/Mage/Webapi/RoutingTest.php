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
    /**
     * TODO: Temporary test for test framework implementation phase
     */
    public function testBasicRouting()
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
}
