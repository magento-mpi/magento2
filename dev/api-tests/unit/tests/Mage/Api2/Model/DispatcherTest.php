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
     * Resource type
     */
    const RESOURCE_TYPE = 'product';

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
            ->setMethods(array(
                'getVersion', 'getModel', 'getParam', 'getApiType', 'getResourceType'
            ))
            ->getMock();
    }

    /**
     * Test instantiate resource class, run resource internal dispatch method
     *
     * @return void
     */
    public function testDispatch()
    {
        $version = 1;
        $lastVersion = 2;
        $userType = 'guest';

        // user mock
        $userMock = $this->getMock('Mage_Api2_Model_Auth_User_Guest', array('getType'));
        $userMock->expects($this->once())
            ->method('getType')
            ->will($this->returnValue($userType));

        // request mock
        $this->_requestMock->expects($this->any())
            ->method('getModel')
            ->will($this->returnValue(self::RESOURCE_MODEL));

        $this->_requestMock->expects($this->any())
            ->method('getApiType')
            ->will($this->returnValue(Mage_Api2_Model_Server::API_TYPE_REST));

        $this->_requestMock->expects($this->any())
            ->method('getResourceType')
            ->will($this->returnValue(self::RESOURCE_TYPE));

        $this->_requestMock->expects($this->any())
            ->method('getVersion')
            ->will($this->returnValue($version));

        // resource model mock
        $modelMock = $this->getModelMockBuilder('api2/dispatcher_testResource_rest_guest_v1')
            ->setMethods(array('setRequest', 'setResponse', 'setApiUser', 'dispatch'))
            ->getMock();

        $modelMock->expects($this->once())
            ->method('setRequest')
            ->with($this->_requestMock);

        $modelMock->expects($this->once())
            ->method('setResponse')
            ->with($this->_response);

        $modelMock->expects($this->once())
            ->method('setApiUser')
            ->with($userMock);

        $modelMock->expects($this->once())
            ->method('dispatch');

        // dispatcher mock
        $dispatcherMock = new Mage_Api2_Model_Dispatcher_Mock();
        $dispatcherMock->setApiUser($userMock)->dispatch($this->_requestMock, $this->_response);
    }

    /**
     * Test failed instantiate resource class
     *
     * @return void
     */
    public function testDispatchFail()
    {
        $invalidVersion = 'INVALID_VERSION';

        $userMock = $this->getMock('Mage_Api2_Model_Auth_User_Guest', array('getType'));

        $userMock->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('guest'));

        $this->_requestMock->expects($this->any())
            ->method('getVersion')
            ->will($this->returnValue($invalidVersion));

        $this->_requestMock->expects($this->any())
            ->method('getModel')
            ->will($this->returnValue(self::RESOURCE_MODEL));

        $this->_requestMock->expects($this->any())
            ->method('getApiType')
            ->will($this->returnValue(Mage_Api2_Model_Server::API_TYPE_REST));

        /** @var $dispatcher Mage_Api2_Model_Dispatcher_Mock */
        $dispatcher = new Mage_Api2_Model_Dispatcher_Mock();

        $this->setExpectedException(
            'Mage_Api2_Exception',
            sprintf('Invalid version "%s" requested.', $invalidVersion),
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

    /**
     * Test that version correctly determined
     */
    public function testGetVersion()
    {
        $dispatcher = new Mage_Api2_Model_Dispatcher_Mock();
        $this->assertEquals(1, $dispatcher->getVersion('product', 1));
        $this->assertEquals(2, $dispatcher->getVersion('product', 2));
        $this->assertEquals(2, $dispatcher->getVersion('product', 3));
        $this->assertEquals(2, $dispatcher->getVersion('product', 4));
        $this->assertEquals(5, $dispatcher->getVersion('product', 5));
        $this->assertEquals(5, $dispatcher->getVersion('product', 6));
        $this->assertEquals(5, $dispatcher->getVersion('product', false));

        try {
            $dispatcher->getVersion('product', 0);
        } catch (Mage_Api2_Exception $e) {
            try {
                $dispatcher->getVersion('product', -1);
            } catch (Mage_Api2_Exception $e) {
                try {
                    $dispatcher->getVersion('product', 1.1);
                } catch (Mage_Api2_Exception $e) {
                    try {
                        $dispatcher->getVersion('product', '1m');
                    } catch (Mage_Api2_Exception $e) {
                        return;
                    }
                }
            }
        }

        $this->fail('Expected exception was not throwed.');
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

    public function getConfig()
    {
        return new Mage_Api2_Model_Config_For_Dispatcher;
    }
}

class Mage_Api2_Model_Config_For_Dispatcher extends Mage_Api2_Model_Config
{
    public function __construct()
    {
        // Load data of config files api2.xml
        $config = Mage::getConfig();

        $mergeModel = new Mage_Core_Model_Config_Base();

        $mergeModel->loadFile(dirname(__FILE__) . DS . '_fixtures' .DS . 'xml' . DS . 'api2.xml');
        $config->extend($mergeModel);
        $this->setXml($config->getNode('api2'));
    }
}
