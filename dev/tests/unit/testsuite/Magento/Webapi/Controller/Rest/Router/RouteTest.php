<?php
/**
 * Test Rest router route.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Router_RouteTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Controller_Rest_Router_Route */
    protected $_restRoute;

    protected function setUp()
    {
        /** Init SUT. */
        $this->_restRoute = new Magento_Webapi_Controller_Rest_Router_Route('route');
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restRoute);
        parent::tearDown();
    }

    /**
     * Test setServiceName and getServiceName methods.
     */
    public function testResourceName()
    {
        /** Assert that new object has no Resource name set. */
        $this->assertNull($this->_restRoute->getServiceId(), 'New object has a set Resource name.');
        /** Set Resource name. */
        $resourceName = 'Resource name';
        $this->_restRoute->setServiceId($resourceName);
        /** Assert that Resource name was set. */
        $this->assertEquals($resourceName, $this->_restRoute->getServiceId(), 'Resource name is wrong.');
    }

    /**
     * Test setServiceType and getServiceType methods.
     */
    public function testResourceType()
    {
        /** Assert that new object has no Resource type set. */
        $this->assertNull($this->_restRoute->getHttpMethod(), 'New object has a set Resource type.');
        /** Set Resource type. */
        $resourceType = 'Resource type';
        $this->_restRoute->setHttpMethod($resourceType);
        /** Assert that Resource type was set. */
        $this->assertEquals($resourceType, $this->_restRoute->getHttpMethod(), 'Resource type is wrong.');
    }

    public function testMatch()
    {
        $areaName = 'rest';
        $testApi = 'test_api';
        $route = new Magento_Webapi_Controller_Rest_Router_Route("$areaName/:$testApi");

        $testUri = "$areaName/$testApi";
        $request = new Zend_Controller_Request_Http();
        $request->setRequestUri($testUri);

        $match = $route->match($request);
        $this->assertEquals($testApi, $match[$testApi], 'Rest route did not match.');
    }
}
