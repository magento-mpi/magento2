<?php
/**
 * Test Rest router route.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Router_Route_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Router_Route_Rest */
    protected $_restRoute;

    protected function setUp()
    {
        /** Init SUT. */
        $this->_restRoute = new Mage_Webapi_Controller_Router_Route_Rest('route');
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
    public function testServiceName()
    {
        /** Assert that new object has no Resource name set. */
        $this->assertNull($this->_restRoute->getServiceName(), 'New object has a set Resource name.');
        /** Set Resource name. */
        $serviceName = 'Resource name';
        $this->_restRoute->setServiceName($serviceName);
        /** Assert that Resource name was set. */
        $this->assertEquals($serviceName, $this->_restRoute->getServiceName(), 'Resource name is wrong.');
    }
}
