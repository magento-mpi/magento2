<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test routing based on a Service that exposes Soap Operations only
 */
class Mage_Webapi_Routing_SoapOnlyTest extends Mage_Webapi_Routing_BaseService
{
    /**
     * @var string
     */
    protected $_version;
    /**
     * @var string
     */
    protected $_restResourcePath;
    /**
     * @var string
     */
    protected $_soapService;

    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_restResourcePath = "/$this->_version/testModule2AllSoapNoRest/";
        $this->_soapService = 'testModule2AllSoapNoRestV1';
    }


    /**
     *  Test get item
     */
    public function testItem()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => Mage_Webapi_Model_Rest_Config::HTTP_METHOD_GET
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Item'
            )
        );
        $requestData = array('id' => $itemId);

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $item = $this->_webApiCall($serviceInfo, $requestData);
            $this->assertEquals($itemId, $item['id'], 'Item was retrieved unsuccessfully');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertNoRestRouteException($serviceInfo, $requestData);
        }

    }

    /**
     * Test fetching all items
     */
    public function testItems()
    {
        $itemArr = array(
            array(
                'id' => 1,
                'name' => 'testItem1'
            ),
            array(
                'id' => 2,
                'name' => 'testItem2'
            )
        );
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => Mage_Webapi_Model_Rest_Config::HTTP_METHOD_GET
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Items'
            )
        );
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $item = $this->_webApiCall($serviceInfo);
            $this->assertEquals($itemArr, $item, 'Items were not retrieved');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertNoRestRouteException($serviceInfo);
        }

    }

    /**
     *  Test create item
     */
    public function testCreate()
    {
        $createdItemName = 'createdItemName';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => Mage_Webapi_Model_Rest_Config::HTTP_METHOD_POST
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Create'
            )
        );
        $requestData = array('name' => $createdItemName);
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $item = $this->_webApiCall($serviceInfo, $requestData);
            $this->assertEquals($createdItemName, $item['name'], 'Item creation failed');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertNoRestRouteException($serviceInfo, $requestData);
        }
    }

    /**
     *  Test update item
     */
    public function testUpdate()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => Mage_Webapi_Model_Rest_Config::HTTP_METHOD_PUT
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Update'
            )
        );
        $requestData = array('id' => $itemId);
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $item = $this->_webApiCall($serviceInfo, $requestData);
            $this->assertEquals($itemId, $item['id'], 'Item update failed');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertNoRestRouteException($serviceInfo, $requestData);
        }
    }

    /**
     *  Test remove item
     */
    public function testRemove()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => Mage_Webapi_Model_Rest_Config::HTTP_METHOD_DELETE
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Remove'
            )
        );
        $requestData = array('id' => $itemId);
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $item = $this->_webApiCall($serviceInfo, $requestData);
            $this->assertEquals($itemId, $item['id'], 'Item remove failed');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertNoRestRouteException($serviceInfo, $requestData);
        }
    }

}
