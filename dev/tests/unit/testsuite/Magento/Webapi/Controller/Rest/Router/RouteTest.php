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
        $this->assertNull($this->_restRoute->getServiceClass(), 'New object has a set Resource name.');
        /** Set Resource name. */
        $resourceName = 'Resource name';
        $this->_restRoute->setServiceClass($resourceName);
        /** Assert that Resource name was set. */
        $this->assertEquals($resourceName, $this->_restRoute->getServiceClass(), 'Resource name is wrong.');
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
