<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Api2 router model
 */
class Mage_Api2_Model_RouterTest extends Mage_PHPUnit_TestCase
{
    /**#@+
     * Resource route values
     */
    const RESOURCE_TYPE  = 'product';
    const RESOURCE_MODEL = 'Mage_Catalog_Model_Product_Api2';
    /**#@- */

    /**
     * Product item id
     */
    const PRODUCT_ID = 2;

    /**
     * Request object
     *
     * @var Mage_Api2_Model_Request
     */
    protected $_request;

    /**
     * Request object
     *
     * @var Mage_Api2_Model_Router
     */
    protected $_router;

    /**
     * Get route (emulates retrieve from helper)
     *
     * @param array $defaults Default values for parameters
     * @return Mage_Api2_Model_Route_Rest
     */
    protected function _getConfigRoute(array $defaults)
    {
        return Mage::getModel(
            'api2/route_rest',
            array(
                Mage_Api2_Model_Route_Abstract::PARAM_ROUTE    => 'products/:id',
                Mage_Api2_Model_Route_Abstract::PARAM_DEFAULTS => $defaults
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

        $this->_request = Mage::getModel('api2/request', null);
        $this->_router  = Mage::getSingleton('api2/router');
    }

    /**
     * Test not matched routes in Request
     */
    public function testRouteNotMatched()
    {
        $this->setExpectedException(
            'Mage_Api2_Exception', 'Request does not match any route.', Mage_Api2_Model_Server::HTTP_NOT_FOUND
        );

        $this->_router->route($this->_request);
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
            ->route($this->_request);

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
            'Mage_Api2_Exception', 'Matched resource is not properly set.', Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
        );

        $this->_router->setRoutes(array($this->_getConfigRoute(array('type' => self::RESOURCE_TYPE))))
            ->route($this->_request);

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
            'Mage_Api2_Exception', 'Matched resource is not properly set.', Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
        );

        $this->_router->setRoutes(array($this->_getConfigRoute(array('model' => self::RESOURCE_MODEL))))
            ->route($this->_request);

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
        $apiTypeRouteMock = $this->getModelMockBuilder('api2/route_apiType')
            ->setConstructorArgs(array(null))
            ->setMethods(array('match'))
            ->getMock();

        $apiTypeRouteMock->expects($this->once())
            ->method('match')
            ->with($this->_request, true)
            ->will($this->returnValue(false));

        $this->setExpectedException(
            'Mage_Api2_Exception', 'Request does not match type route.', Mage_Api2_Model_Server::HTTP_NOT_FOUND
        );

        $this->_router->routeApiType($this->_request);
    }
}
