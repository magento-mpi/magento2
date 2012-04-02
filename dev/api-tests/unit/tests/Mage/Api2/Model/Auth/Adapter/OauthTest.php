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
 * Test Api2 Auth model
 */
class Mage_Api2_Model_Auth_Adapter_OauthTest extends Mage_PHPUnit_TestCase
{
    /**
     * Authentication adapter object
     *
     * @var Mage_Api2_Model_Auth_Adapter_Oauth
     */
    protected $_adapter;

    /**
     * Request object
     *
     * @var Mage_Api2_Model_Request
     */
    protected $_request;

    /**
     * Prepares initializers.
     * Is called in setUp method first. Can be overridden in testCases to add more initializers.
     */
    protected function prepareInitializers()
    {
        parent::prepareInitializers();

        Mage_PHPUnit_Initializer_Factory::createInitializer('Mage_PHPUnit_Initializer_HeadersAlreadySent');
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_request = Mage::getSingleton('api2/request');
        $this->_adapter = Mage::getModel('api2/auth_adapter_oauth');
    }

    /**
     * Test getUserParams method
     */
    public function testGetUserParamsAuthorizationAdmin()
    {
        $_SERVER['HTTP_HOST']          = 'testhost.com';
        $_SERVER['REQUEST_URI']        = '/testuri/';
        $_SERVER['HTTP_AUTHORIZATION'] = 'OAuth realm="Test Realm"';

        $oauthServer = $this->getModelMockBuilder('oauth/server')->setMethods(array('checkAccessRequest'))->getMock();
        $oauthToken  = $this->getModelMockBuilder('oauth/token')
            ->setMethods(array('getUserType', 'getAdminId'))
            ->getMock();

        $oauthServer->expects($this->once())
            ->method('checkAccessRequest')
            ->will($this->returnValue($oauthToken));

        $oauthToken->expects($this->once())
            ->method('getUserType')
            ->will($this->returnValue('admin'));
        $oauthToken->expects($this->once())
            ->method('getAdminId')
            ->will($this->returnValue(5));

        $userParams = $this->_adapter->getUserParams($this->_request);

        $this->assertInstanceOf('stdClass', $userParams);
        $this->assertObjectHasAttribute('type', $userParams);
        $this->assertObjectHasAttribute('id', $userParams);
        $this->assertEquals('admin', $userParams->type, 'User role does not match');
        $this->assertEquals(5, $userParams->id, 'User identifier does not match');
    }

    /**
     * Test getUserParams method
     */
    public function testGetUserParamsAuthorizationCustomer()
    {
        $_SERVER['HTTP_HOST']          = 'testhost.com';
        $_SERVER['REQUEST_URI']        = '/testuri/';
        $_SERVER['HTTP_AUTHORIZATION'] = 'OAuth realm="Test Realm"';

        $oauthServer = $this->getModelMockBuilder('oauth/server')->setMethods(array('checkAccessRequest'))->getMock();
        $oauthToken  = $this->getModelMockBuilder('oauth/token')
            ->setMethods(array('getUserType', 'getCustomerId'))
            ->getMock();

        $oauthServer->expects($this->once())
            ->method('checkAccessRequest')
            ->will($this->returnValue($oauthToken));

        $oauthToken->expects($this->once())
            ->method('getUserType')
            ->will($this->returnValue('customer'));
        $oauthToken->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue(5));

        $userParams = $this->_adapter->getUserParams($this->_request);

        $this->assertInstanceOf('stdClass', $userParams);
        $this->assertObjectHasAttribute('type', $userParams);
        $this->assertObjectHasAttribute('id', $userParams);
        $this->assertEquals('customer', $userParams->type, 'User role does not match');
        $this->assertEquals(5, $userParams->id, 'User identifier does not match');
    }

    /**
     * Test getUserParams method
     */
    public function testGetUserParamsAuthorizationAuthInvalid()
    {
        $_SERVER['HTTP_HOST']          = 'testhost.com';
        $_SERVER['REQUEST_URI']        = '/testuri/';
        $_SERVER['HTTP_AUTHORIZATION'] = 'OAuth realm="Test Realm"';

        $this->setExpectedException('Mage_Api2_Exception', '', Mage_Api2_Model_Server::HTTP_UNAUTHORIZED);

        $this->_adapter->getUserParams($this->_request);
    }

    /**
     * Test IsApplicableToRequest method
     */
    public function testIsApplicableToRequestAuthorizationHeaderInvalid()
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'NotOAuth realm="Test Realm"';

        $this->assertFalse($this->_adapter->isApplicableToRequest($this->_request));
    }

    /**
     * Test IsApplicableToRequest method
     */
    public function testIsApplicableToRequestNoAuthorizationHeader()
    {
        $this->assertFalse($this->_adapter->isApplicableToRequest($this->_request));
    }
}
