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
    /**
     * Message of message of raised exception fail
     */
    const RAISED_EXCEPTION_FAIL_MESSAGE = 'An expected Mage_Api2_Exception has not been raised.';

    /**#@+
     * Resource values
     */
    const RESOURCE_TYPE = 'product';
    const RESOURCE_MODEL = 'Mage_Catalog_Model_Product_Api2';
    /**#@- */

    /**
     * Product item id
     */
    const PRODUCT_ID = 2;

    /**
     * Get route
     *
     * @param array $options
     * @return Mage_Api2_Model_Route_Rest
     */
    protected function _getConfigRoute(array $options)
    {
        $arguments = array(
            Mage_Api2_Model_Route_Abstract::PARAM_ROUTE    => 'products/:id',
            Mage_Api2_Model_Route_Abstract::PARAM_DEFAULTS => $options
        );

        /** @var $rest Mage_Api2_Model_Route_Rest */
        $rest = Mage::getModel('api2/route_rest', $arguments);

        return $rest;
    }

    /**
     * Get Request object
     *
     * @return Mage_Api2_Model_Request
     */
    protected function _getRequest()
    {
        $baseUrl = strtr(Mage_Api2_Model_Request::BASE_URL, array(':api' => Mage_Api2_Model_Server::API_TYPE_REST));

        /** @var $request Mage_Api2_Model_Request */
        $request = Mage::getSingleton('api2/request');
        $request->setRequestUri($baseUrl . 'products/' . self::PRODUCT_ID)
            ->setBaseUrl($baseUrl);

        return $request;
    }

    /**
     * Test not matched routes in Request
     *
     * @return void
     */
    public function testNotMatchedRoute()
    {
        /** @var $request Mage_Api2_Model_Request */
        $request = Mage::getSingleton('api2/request');
        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getSingleton('api2/router');

        try {
            $router->route($request);
        } catch (Mage_Api2_Exception $e) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $e->getCode());
            $this->assertEquals('Request not matched any route.', $e->getMessage());
            return;
        }

        $this->fail(self::RAISED_EXCEPTION_FAIL_MESSAGE);
    }

    /**
     * Test routes match and set params to Request
     *
     * @return void
     */
    public function testRoute()
    {
        $request = $this->_getRequest();

        $this->assertNull($request->getParam('id'));
        $this->assertNull($request->getParam('type'));
        $this->assertNull($request->getParam('model'));

        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getSingleton('api2/router');

        $options = array(
            'model' => self::RESOURCE_MODEL,
            'type'  => self::RESOURCE_TYPE,
        );

        $router->setRoutes(array($this->_getConfigRoute($options)))
            ->route($request);

        $this->assertEquals(self::PRODUCT_ID, $request->getParam('id'));
        $this->assertEquals('product', $request->getParam('type'));
        $this->assertEquals(self::RESOURCE_MODEL, $request->getParam('model'));
    }

    /**
     * Test routes match and set params to Request with wrong routes (without resource model tag)
     *
     * @return void
     */
    public function testSetRequestParamsWithoutResourceModel()
    {
        $request = $this->_getRequest();

        $this->assertNull($request->getParam('id'));
        $this->assertNull($request->getParam('type'));
        $this->assertNull($request->getParam('model'));

        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getSingleton('api2/router');

        $options = array(
            'type'  => self::RESOURCE_TYPE
        );

        try {
            $router->setRoutes(array($this->_getConfigRoute($options)))
                ->route($request);
        } catch (Mage_Api2_Exception $e) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR, $e->getCode());
            $this->assertEquals('Matched resource is not properly set.', $e->getMessage());
            $this->assertNull($request->getParam('id'));
            $this->assertNull($request->getParam('type'));
            $this->assertNull($request->getParam('model'));
            return;
        }

        $this->fail(self::RAISED_EXCEPTION_FAIL_MESSAGE);
    }

    /**
     * Test routes match and set params to Request with wrong routes (without resource type tag)
     *
     * @return void
     */
    public function testSetRequestParamsWithoutResourceType()
    {
        $request = $this->_getRequest();

        $this->assertNull($request->getParam('id'));
        $this->assertNull($request->getParam('type'));
        $this->assertNull($request->getParam('model'));

        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getSingleton('api2/router');

        $options = array(
            'model' => self::RESOURCE_MODEL
        );

        try {
            $router->setRoutes(array($this->_getConfigRoute($options)))
                ->route($request);
        } catch (Mage_Api2_Exception $e) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR, $e->getCode());
            $this->assertEquals('Matched resource is not properly set.', $e->getMessage());
            $this->assertNull($request->getParam('id'));
            $this->assertNull($request->getParam('type'));
            $this->assertNull($request->getParam('model'));
            return;
        }

        $this->fail(self::RAISED_EXCEPTION_FAIL_MESSAGE);
    }

    /**
     * Test routes match and set params to Request with wrong routes (without resource tags)
     *
     * @return void
     */
    public function testSetRequestParamsWithoutResourceTags()
    {
        $request = $this->_getRequest();

        $this->assertNull($request->getParam('id'));
        $this->assertNull($request->getParam('type'));
        $this->assertNull($request->getParam('model'));

        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getSingleton('api2/router');

        try {
            $router->setRoutes(array($this->_getConfigRoute(array())))
                ->route($request);
        } catch (Mage_Api2_Exception $e) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR, $e->getCode());
            $this->assertEquals('Matched resource is not properly set.', $e->getMessage());
            $this->assertNull($request->getParam('id'));
            $this->assertNull($request->getParam('type'));
            $this->assertNull($request->getParam('model'));
            return;
        }

        $this->fail(self::RAISED_EXCEPTION_FAIL_MESSAGE);
    }
}
