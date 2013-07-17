<?php
/**
 * Test NoWebApiXmlTestTest TestModule2
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_TestModule2_Service_NoWebApiXmlTestTest extends Magento_Test_TestCase_WebapiAbstract
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
        $this->_restResourcePath = "/$this->_version/testModule2NoWebApiXml/";
        $this->_soapService = 'testModule2NoWebApiXml';
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
        $this->assertNoRouteOrOperationException($serviceInfo, $requestData);
    }

    /**
     * Test fetching all items
     */
    public function testItems()
    {
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
        $this->assertNoRouteOrOperationException($serviceInfo);
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
        $this->assertNoRouteOrOperationException($serviceInfo, $requestData);
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
        $this->assertNoRouteOrOperationException($serviceInfo, $requestData);
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
        $this->assertNoRouteOrOperationException($serviceInfo, $requestData);
    }
}
