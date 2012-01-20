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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Api2 Dispatcher model
 */
class Mage_Api2_Model_DispatcherTest extends Mage_PHPUnit_TestCase
{
    /**
     * API version
     */
    const API_VERSION = 1;

    /**#@+
     * Resource values
     */
    const RESOURCE_TYPE  = 'product';
    const RESOURCE_MODEL = 'Mage_Api2_Model_Dispatcher_TestResource';
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
    protected $_requestMock;

    /**
     * Response object
     *
     * @var Mage_Api2_Model_Response
     */
    protected $_response;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $baseUrl = strtr(Mage_Api2_Model_Request::BASE_URL, array(':api' => Mage_Api2_Model_Server::API_TYPE_REST));

        $this->_requestMock = $this->getSingletonMockBuilder('api2/request')
            ->setMethods(array('getVersion'))
            ->getMock();

        $this->_requestMock->setRequestUri($baseUrl . 'products/' . self::PRODUCT_ID)
            ->setBaseUrl($baseUrl);

        $this->_response = Mage::getSingleton('api2/response');
    }

    /**
     * Retrieve User object
     *
     * @param string $name
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    protected function _getUser($name)
    {
        return Mage::getModel('api2/auth_user_' . $name);
    }

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
     * Test instantiate resource class, run resource internal dispatch method
     *
     * @return void
     */
    public function testDispatch()
    {
        $this->_requestMock->expects($this->once())
            ->method('getVersion')
            ->will($this->returnValue(self::API_VERSION));

        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getSingleton('api2/router');

        $options = array(
            'model' => self::RESOURCE_MODEL,
            'type'  => self::RESOURCE_TYPE,
        );

        $router->setRoutes(array($this->_getConfigRoute($options)))
            ->route($this->_requestMock);

        /** @var $dispatcher Mage_Api2_Model_Dispatcher */
        $dispatcher = Mage::getModel('api2/dispatcher', $this->_getUser('guest'));
        $this->assertTrue($dispatcher->dispatch($this->_requestMock, $this->_response) instanceof Mage_Api2_Model_Dispatcher);
    }

    /**
     * Test failed instantiate resource class
     *
     * @return void
     */
    public function testDispatchFail()
    {
        $apiVersion = 2;

        $this->_requestMock->expects($this->once())
            ->method('getVersion')
            ->will($this->returnValue($apiVersion));

        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getSingleton('api2/router');

        $options = array(
            'model' => self::RESOURCE_MODEL,
            'type'  => self::RESOURCE_TYPE,
        );

        $router->setRoutes(array($this->_getConfigRoute($options)))
            ->route($this->_requestMock);

        $userType = 'guest';

        /** @var $dispatcher Mage_Api2_Model_Dispatcher */
        $dispatcher = Mage::getModel('api2/dispatcher', $this->_getUser($userType));

        try {
            $dispatcher->dispatch($this->_requestMock, $this->_response);
        } catch (Mage_Api2_Exception $e) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR, $e->getCode());

            $class = sprintf(
                'Invalid resource class "%s_%s_%s_V%d"',
                self::RESOURCE_MODEL,
                ucfirst(Mage_Api2_Model_Server::API_TYPE_REST),
                ucfirst($userType),
                $apiVersion
            );
            $this->assertEquals($class, $e->getMessage());
            return;
        }

        $this->fail('An expected Mage_Api2_Exception has not been raised.');
    }

    /**
     * Test set api user to class property
     *
     * @return void
     */
    public function testSetApiUser()
    {
        /** @var $dispatcher Mage_Api2_Model_Dispatcher */
        $dispatcher = Mage::getModel('api2/dispatcher', $this->_getUser('guest'));
        $this->assertInstanceOf('Mage_Api2_Model_Dispatcher', $dispatcher->setApiUser($this->_getUser('customer')));
    }

    /**
     * Test failed set api user to class property
     *
     * @expectedException Exception
     * @return void
     */
    public function testSetApiUserFail()
    {
        Mage::getModel('api2/dispatcher', 'qwerty');
    }
}
