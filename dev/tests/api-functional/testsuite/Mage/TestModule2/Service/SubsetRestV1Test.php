<?php
/**
 * Test SubsetRestV1Test TestModule2
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_TestModule2_Service_SubsetRestV1Test extends Magento_Test_TestCase_WebapiAbstract
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
        $this->_restResourcePath = "/$this->_version/testModule2SubsetRest/";
        $this->_soapService = 'testModule2SubsetRest';
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
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], 'Item was retrieved unsuccessfully');


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

        $item = $this->_webApiCall($serviceInfo);
        $this->assertEquals($itemArr, $item, 'Items were not retrieved');

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
     * This is a helper function to invoke the REST api and assert for the SubsetRestV1Test
     * test cases that no such REST route exists
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
                '{"errors":[{"code":404,"message":"Request does not match any route."}]}',
                sprintf(
                    'REST routing did not fail as expected for Resource "%s" and method "%s"',
                    $serviceInfo['rest']['resourcePath'],
                    $serviceInfo['rest']['httpMethod']
                )
            );
        }
    }
}
