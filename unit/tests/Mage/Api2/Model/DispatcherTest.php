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
     * Resource model name prefix
     */
    const RESOURCE_MODEL = 'api2/dispatcher_testResource';

    /**
     * Request object
     *
     * @var PHPUnit_Framework_MockObject_MockObject
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

        $this->_response = Mage::getSingleton('api2/response');

        $this->_requestMock = $this->getSingletonMockBuilder('api2/request')
            ->setMethods(array('getVersion', 'getModel', 'getParam', 'getApiType'))
            ->getMock();
    }

    /**
     * Test instantiate resource class, run resource internal dispatch method
     *
     * @return void
     */
    public function testDispatch()
    {
        $userMock = $this->getMock('Mage_Api2_Model_Auth_User_Guest');

        $userMock->expects($this->once())
            ->method('getRole')
            ->will($this->returnValue('guest'));

        $this->_requestMock->expects($this->any())
            ->method('getVersion')
            ->will($this->returnValue(1));

        $this->_requestMock->expects($this->any())
            ->method('getModel')
            ->will($this->returnValue(self::RESOURCE_MODEL));

        $this->_requestMock->expects($this->once())
            ->method('getParam')
            ->with('type')
            ->will($this->returnValue('product'));

        $this->_requestMock->expects($this->any())
            ->method('getApiType')
            ->will($this->returnValue(Mage_Api2_Model_Server::API_TYPE_REST));

        $dispatcher = new Mage_Api2_Model_Dispatcher($userMock);
        $dispatcher->setApiUser($userMock)->dispatch($this->_requestMock, $this->_response);
    }

    /**
     * Test failed instantiate resource class
     *
     * @return void
     */
    public function testDispatchFail()
    {
        $userMock = $this->getMock('Mage_Api2_Model_Auth_User_Guest', array('getRole'));

        $userMock->expects($this->once())
            ->method('getRole')
            ->will($this->returnValue('guest'));

        $this->_requestMock->expects($this->any())
            ->method('getVersion')
            ->will($this->returnValue('INVALID_VERSION'));

        $this->_requestMock->expects($this->any())
            ->method('getModel')
            ->will($this->returnValue(self::RESOURCE_MODEL));

        $this->_requestMock->expects($this->any())
            ->method('getApiType')
            ->will($this->returnValue(Mage_Api2_Model_Server::API_TYPE_REST));

        /** @var $dispatcher Mage_Api2_Model_Dispatcher */
        $dispatcher = new Mage_Api2_Model_Dispatcher($userMock);

        $this->setExpectedException(
            'Mage_Api2_Exception',
            'Resource is not found',
            Mage_Api2_Model_Server::HTTP_BAD_REQUEST
        );

        $dispatcher->setApiUser($userMock)->dispatch($this->_requestMock, $this->_response);
    }

    /**
     * Test set api user to class property
     *
     * @return void
     */
    public function testSetApiUser()
    {
        /** @var $userMock Mage_Api2_Model_Auth_User_Abstract */
        $userMock = $this->getMockForAbstractClass('Mage_Api2_Model_Auth_User_Abstract');
        /** @var $dispatcher Mage_Api2_Model_Dispatcher_Mock */
        $dispatcher = new Mage_Api2_Model_Dispatcher_Mock();

        $dispatcher->setApiUser($userMock);

        $this->assertSame($userMock, $dispatcher->_apiUser);
    }
}

/**
 * Webservice api2 dispatcher model mock
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Dispatcher_Mock extends Mage_Api2_Model_Dispatcher
{
    /**
     * API User object
     * Make property public for test purposes
     *
     * @var Mage_Api2_Model_Auth_User_Abstract
     */
    public $_apiUser;
}
