<?php
/**
 * Test AllSoapAndRestV1Test TestModule1
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_TestModule1_Service_AllSoapAndRestV1Test extends Magento_Test_TestCase_WebapiAbstract
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
        $this->_restResourcePath = "/$this->_version/testmodule1/";
        $this->_soapService = 'testModule1AllSoapAndRest';
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
                'name' => 'testProduct1'
            ),
            array(
                'id' => 2,
                'name' => 'testProduct2'
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
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($createdItemName, $item['name'], 'Item creation failed');
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
        $requestData = array('id' => $itemId, 'name' => 'testName');
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals('Updated' . $requestData['name'], $item['name'], 'Item update failed');
    }
}
