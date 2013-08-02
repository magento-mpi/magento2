<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test routing with a service that has no webapi.xml
 */
class Mage_TestModule2_Service_NoWebApiXmlTestTest extends Mage_Webapi_Routing_BaseService
{
    /**
     * @var string
     */
    private $_version;
    /**
     * @var string
     */
    private $_restResourcePath;

    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_restResourcePath = "/$this->_version/testModule2NoWebApiXml/";
        $this->_soapService = 'testModule2NoWebApiXmlV1';
    }


    /**
     *  Test get item
     */
    public function testItem()
    {
        $this->_markTestAsRestOnly();
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => 'GET'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Item'
            )
        );
        $requestData = array('id' => $itemId);
        $this->assertNoRestRouteException($serviceInfo, $requestData);
    }

    /**
     * Test fetching all items
     */
    public function testItems()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => 'GET'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Items'
            )
        );
        $this->assertNoRestRouteException($serviceInfo);
    }

    /**
     *  Test create item
     */
    public function testCreate()
    {
        $this->_markTestAsRestOnly();
        $createdItemName = 'createdItemName';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath,
                'httpMethod' => 'POST'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Create'
            )
        );
        $requestData = array('name' => $createdItemName);
        $this->assertNoRestRouteException($serviceInfo, $requestData);
    }

    /**
     *  Test update item
     */
    public function testUpdate()
    {
        $this->_markTestAsRestOnly();
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
        $this->assertNoRestRouteException($serviceInfo, $requestData);
    }

    /**
     *  Test remove item
     */
    public function testRemove()
    {
        $this->_markTestAsRestOnly();
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => $this->_restResourcePath . $itemId,
                'httpMethod' => 'DELETE'
            ),
            'soap' => array(
                'service' => $this->_soapService,
                'operation' => $this->_soapService . 'Remove'
            )
        );
        $requestData = array('id' => $itemId);
        $this->assertNoRestRouteException($serviceInfo, $requestData);
    }
}
