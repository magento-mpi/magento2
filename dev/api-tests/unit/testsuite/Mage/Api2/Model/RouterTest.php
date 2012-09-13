<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Webapi router model
 */
class Mage_Webapi_Model_RouterTest extends Mage_PHPUnit_TestCase
{
    /**#@+
     * Resource route values
     */
    const RESOURCE_TYPE  = 'product';
    const RESOURCE_MODEL = 'Mage_Catalog_Model_Product_Webapi';
    /**#@- */

    /**
     * Product item id
     */
    const PRODUCT_ID = 2;

    /**
     * Request object
     *
     * @var Mage_Webapi_Model_Request
     */
    protected $_request;

    /**
     * Request object
     *
     * @var Mage_Webapi_Model_Router
     */
    protected $_router;

    /**
     * Get route (emulates retrieve from helper)
     *
     * @param array $defaults Default values for parameters
     * @return Mage_Webapi_Model_Route_Rest
     */
    protected function _getConfigRoute(array $defaults)
    {
        return Mage::getModel(
            'Mage_Webapi_Model_Route_Rest',
            array(
                Mage_Webapi_Model_Route_Abstract::PARAM_ROUTE    => 'products/:id',
                Mage_Webapi_Model_Route_Abstract::PARAM_DEFAULTS => $defaults
            )
        );
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_request = Mage::getModel('Mage_Webapi_Model_Request', null);
        $this->_router  = Mage::getSingleton('Mage_Webapi_Model_Router');
    }

    /**
     * Test not matched routes in Request
     */
    public function testRouteNotMatched()
    {
        $this->setExpectedException(
            'Mage_Webapi_Exception', 'Request does not match any route.', Mage_Webapi_Controller_Front_Rest::HTTP_NOT_FOUND
        );

        $this->_router->match($this->_request);
    }

    /**
     * Test routes match and set params to Request
     */
    public function testRoute()
    {
        $this->_request->setRequestUri('/products/' . self::PRODUCT_ID);

        $this->assertNull($this->_request->getParam('id'));
        $this->assertNull($this->_request->getParam('type'));
        $this->assertNull($this->_request->getParam('model'));

        $options = array('model' => self::RESOURCE_MODEL, 'type' => self::RESOURCE_TYPE);

        $this->_router->setRoutes(array($this->_getConfigRoute($options)))
            ->match($this->_request);

        $this->assertEquals(self::PRODUCT_ID, $this->_request->getParam('id'));
        $this->assertEquals(self::RESOURCE_TYPE, $this->_request->getParam('type'));
        $this->assertEquals(self::RESOURCE_MODEL, $this->_request->getParam('model'));
    }

    /**
     * Test routes match and set params to Request with wrong routes (without resource model tag)
     */
    public function testSetRequestParamsWithoutResourceModel()
    {
        $this->_request->setRequestUri('/products/' . self::PRODUCT_ID);

        $this->assertNull($this->_request->getParam('type'));
        $this->setExpectedException(
            'Mage_Webapi_Exception', 'Matched resource is not properly set.', Mage_Webapi_Controller_Front_Rest::HTTP_INTERNAL_ERROR
        );

        $this->_router->setRoutes(array($this->_getConfigRoute(array('type' => self::RESOURCE_TYPE))))
            ->match($this->_request);

        $this->assertNull($this->_request->getParam('type'));
    }

    /**
     * Test routes match and set params to Request with wrong routes (without resource type tag)
     */
    public function testSetRequestParamsWithoutResourceType()
    {
        $this->_request->setRequestUri('/products/' . self::PRODUCT_ID);

        $this->assertNull($this->_request->getParam('model'));
        $this->setExpectedException(
            'Mage_Webapi_Exception', 'Matched resource is not properly set.', Mage_Webapi_Controller_Front_Rest::HTTP_INTERNAL_ERROR
        );

        $this->_router->setRoutes(array($this->_getConfigRoute(array('model' => self::RESOURCE_MODEL))))
            ->match($this->_request);

        $this->assertNull($this->_request->getParam('model'));
    }

    /**
     * Test routeApiType() with $trimApiTypePath = true
     */
    public function testRouteApiTypeTrimPath()
    {
        $apiType = 'rest';

        $this->_request->setRequestUri("/api/{$apiType}/products/15");

        $this->assertNull($this->_request->getApiType());

        $this->_router->routeApiType($this->_request, true);

        $this->assertEquals('/products/15', $this->_request->getPathInfo());
        $this->assertEquals($apiType, $this->_request->getApiType());
    }

    /**
     * Test routeApiType() with $trimApiTypePath = false
     */
    public function testRouteApiTypeNoTrimPath()
    {
        $apiType = 'rest';

        $this->_request->setRequestUri("/api/{$apiType}/products/15");

        $initialPathInfo = $this->_request->getPathInfo();

        $this->assertNull($this->_request->getApiType());

        $this->_router->routeApiType($this->_request, false);

        $this->assertEquals($initialPathInfo, $this->_request->getPathInfo());
        $this->assertEquals($apiType, $this->_request->getApiType());
    }

    /**
     * Test routeApiType() with URI does not match
     */
    public function testRouteApiTypeNotMatch()
    {
        $apiTypeRouteMock = $this->getModelMockBuilder('Mage_Webapi_Model_Route_ApiType')
            ->setConstructorArgs(array(null))
            ->setMethods(array('match'))
            ->getMock();

        $apiTypeRouteMock->expects($this->once())
            ->method('match')
            ->with($this->_request, true)
            ->will($this->returnValue(false));

        $this->setExpectedException(
            'Mage_Webapi_Exception', 'Request does not match type route.', Mage_Webapi_Controller_Front_Rest::HTTP_NOT_FOUND
        );

        $this->_router->routeApiType($this->_request);
    }
}
