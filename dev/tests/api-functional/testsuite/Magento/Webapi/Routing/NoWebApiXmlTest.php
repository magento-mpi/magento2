<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Routing;

use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class to test routing with a service that has no webapi.xml
 */
class NoWebApiXmlTest extends \Magento\Webapi\Routing\BaseService
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
        $this->_restResourcePath = "/{$this->_version}/testModule2NoWebApiXml/";
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
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            )
        );
        $requestData = array('id' => $itemId);
        $this->_assertNoRestRouteException($serviceInfo, $requestData);
    }

    /**
     * Test fetching all items
     */
    public function testItems()
    {
        $this->_markTestAsRestOnly();
        $serviceInfo = array(
            'rest' => array('resourcePath' => $this->_restResourcePath, 'httpMethod' => RestConfig::HTTP_METHOD_GET)
        );
        $this->_assertNoRestRouteException($serviceInfo);
    }

    /**
     *  Test create item
     */
    public function testCreate()
    {
        $this->_markTestAsRestOnly();
        $createdItemName = 'createdItemName';
        $serviceInfo = array(
            'rest' => array('resourcePath' => $this->_restResourcePath, 'httpMethod' => RestConfig::HTTP_METHOD_POST)
        );
        $requestData = array('name' => $createdItemName);
        $this->_assertNoRestRouteException($serviceInfo, $requestData);
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
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            )
        );
        $requestData = array('id' => $itemId);
        $this->_assertNoRestRouteException($serviceInfo, $requestData);
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
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            )
        );
        $requestData = array('id' => $itemId);
        $this->_assertNoRestRouteException($serviceInfo, $requestData);
    }
}
