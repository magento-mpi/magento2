<?php
/**
 * Test Rest router route.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Router_Route_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Router\Route\Rest */
    protected $_restRoute;

    protected function setUp()
    {
        /** Init SUT. */
        $this->_restRoute = new \Magento\Webapi\Controller\Router\Route\Rest('route');
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restRoute);
        parent::tearDown();
    }

    /**
     * Test setResourceName and getResourceName methods.
     */
    public function testResourceName()
    {
        /** Assert that new object has no Resource name set. */
        $this->assertNull($this->_restRoute->getResourceName(), 'New object has a set Resource name.');
        /** Set Resource name. */
        $resourceName = 'Resource name';
        $this->_restRoute->setResourceName($resourceName);
        /** Assert that Resource name was set. */
        $this->assertEquals($resourceName, $this->_restRoute->getResourceName(), 'Resource name is wrong.');
    }

    /**
     * Test setResourceType and getResourceType methods.
     */
    public function testResourceType()
    {
        /** Assert that new object has no Resource type set. */
        $this->assertNull($this->_restRoute->getResourceType(), 'New object has a set Resource type.');
        /** Set Resource type. */
        $resourceType = 'Resource type';
        $this->_restRoute->setResourceType($resourceType);
        /** Assert that Resource type was set. */
        $this->assertEquals($resourceType, $this->_restRoute->getResourceType(), 'Resource type is wrong.');
    }
}
