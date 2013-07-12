<?php
/**
 * Test AllSoapAndRestV2Test in TestModule1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_TestModule1_Service_AllSoapAndRestV2Test extends Mage_TestModule1_Service_AllSoapAndRestV1Test
{

    /**
     * @override
     */
    protected function setUp()
    {
        $this->_version = 'V2';
        $this->_restResourcePath = "/$this->_version/testmodule1/";
        $this->_soapService = 'testModule1AllSoapAndRest';
    }

    /**
     *  Test delete item
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