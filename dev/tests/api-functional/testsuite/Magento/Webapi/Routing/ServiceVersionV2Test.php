<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test routing based on Service Versioning(for a new version V2 of an existing V1 service)
 */
namespace Magento\Webapi\Routing;

class ServiceVersionV2Test extends \Magento\Webapi\Routing\ServiceVersionV1Test
{

    /**
     * @override
     */
    protected function setUp()
    {
        $this->_version = 'V2';
        $this->_soapService = 'testModule1AllSoapAndRestV2';
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
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
            'soap' => array(
                'service' => $this->_soapService,
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
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_DELETE
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Delete'
            )
        );
        $requestData = array('id' => $itemId, 'name' => 'testName');
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], "Item delete failed");
    }
}
