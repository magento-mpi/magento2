<?php
/**
 * Test AllSoapNoRestV1Test TestModule2
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_TestModule2_Service_AllSoapNoRestV1Test extends Magento_Test_TestCase_WebapiAbstract
{
    /**
     * @var string
     */
    private $_version;
    /**
     * @var string
     */
    private $_restResourcePath;
    /**
     * @var string
     */
    private $_soapService;

    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_restResourcePath = "/$this->_version/testModule2AllSoapNoRest/";
        $this->_soapService = 'testModule2AllSoapNoRest';
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
                'httpMethod' => 'GET'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Item'
            )
        );
        $requestData = array('id' => $itemId);

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $item = $this->_webApiCall($serviceInfo, $requestData);
            $this->assertEquals($itemId, $item['id'], 'Item was retrieved unsuccessfully');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertRestException($serviceInfo, $requestData);
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
                'httpMethod' => 'GET'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Items'
            )
        );
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $item = $this->_webApiCall($serviceInfo);
            $this->assertEquals($itemArr, $item, 'Items were not retrieved');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertRestException($serviceInfo);
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
                'httpMethod' => 'POST'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Create'
            )
        );
        $requestData = array('name' => $createdItemName);
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $item = $this->_webApiCall($serviceInfo, $requestData);
            $this->assertEquals($createdItemName, $item['name'], 'Item creation failed');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertRestException($serviceInfo, $requestData);
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
                'httpMethod' => 'PUT'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Update'
            )
        );
        $requestData = array('id' => $itemId);
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $item = $this->_webApiCall($serviceInfo, $requestData);
            $this->assertEquals($itemId, $item['id'], 'Item update failed');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertRestException($serviceInfo, $requestData);
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
                'httpMethod' => 'DELETE'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'serviceVersion' => $this->_version,
                'operation' => $this->_soapService . 'Remove'
            )
        );
        $requestData = array('id' => $itemId);
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $item = $this->_webApiCall($serviceInfo, $requestData);
            $this->assertEquals($itemId, $item['id'], 'Item remove failed');
        } else if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->assertRestException($serviceInfo, $requestData);
        }
    }

    /**
     * This is a helper function to invoke the REST api and assert for the AllSoapNoRestV1Test
     * test cases that no such REST route exist
     *
     * @param $serviceInfo
     * @param $requestData
     */
    protected function assertRestException($serviceInfo, $requestData = null)
    {
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch (Exception $e) {
            $this->assertEquals(
                $e->getMessage(),
                '{"errors":[{"code":404,"message":"Request does not match any route."}]}'
            );
        }
    }
}
