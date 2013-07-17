<?php
/**
 * Test AllSoapAndRestV2Test in TestModule1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Routing_ServiceVersionV2Test extends Mage_Webapi_Routing_ServiceVersionV1Test
{

    /**
     * @override
     */
    protected function setUp()
    {
        $this->_version = 'V2';
        $this->_restResourcePath = "/$this->_version/testmodule1/";
    }


    /**
     *  Test to assert overriding of the existing 'Item' api in V2 version of the same service
     */
    public function testItem()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => 'GET'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Item'
            )
        );
        $requestData = array('id' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        // Asserting for additional attribute returned by the V2 api
        $this->assertEquals(1, $item['price'], 'Item was retrieved unsuccessfully from V2');
    }


    /**
     *  Test to assert presence of new 'delete' api added in V2 version of the same service
     */
    public function testDelete()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => 'DELETE'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Delete'
            )
        );
        $requestData = array('id' => $itemId, 'name' => 'testName');
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], "Item delete failed");
    }
}