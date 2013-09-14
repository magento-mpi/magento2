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
class Magento_Webapi_Routing_NoWebApiXmlTest extends Magento_Webapi_Routing_BaseService
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
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET
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
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_GET
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
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_POST
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
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_PUT
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
                'httpMethod' => Magento_Webapi_Model_Rest_Config::HTTP_METHOD_DELETE
            )
        );
        $requestData = array('id' => $itemId);
        $this->assertNoRestRouteException($serviceInfo, $requestData);
    }
}
