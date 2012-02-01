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
 * Resource abstract model test
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_ResourceTest extends Mage_PHPUnit_TestCase
{
    /**
     * Abstract resource mock object
     *
     * @var Mage_Api2_Model_ResourceMock
     */
    protected $_resource;

    /**
     * Request object
     *
     * @var Mage_Api2_Model_Request
     */
    protected $_request;

    /**
     * Request object
     *
     * @var Mage_Api2_Model_Response
     */
    protected $_response;

    /**
     * Guest user object
     *
     * @var Mage_Api2_Model_Auth_User_Guest
     */
    protected $_guest;

    /**
     * Initialize additional objects used in tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_request = Mage::getSingleton('api2/request');
        $this->_response = Mage::getSingleton('api2/response');
        $this->_resource = $this->getMockForAbstractClass('Mage_Api2_Model_ResourceMock');
        $this->_guest = Mage::getSingleton('api2/auth_user_guest');
    }

    /**
     * Test version setter and getter
     */
    public function testVersionAccessors()
    {
        // test default version getter
        $versionFromRequest = 4;
        $_SERVER['HTTP_VERSION'] = $versionFromRequest;
        $this->_resource->setRequest($this->_request);
        $this->assertEquals($versionFromRequest, $this->_resource->getVersion());

        // test preset version getting
        $version = 5;
        $this->_resource->setVersion($version);
        $this->assertEquals($version, $this->_resource->getVersion());
    }

    /**
     * Test user type setter and getter
     */
    public function testUserTypeAccessors()
    {
        // test default user type getting
        $this->_resource->setApiUser($this->_guest);
        $this->assertEquals($this->_guest->getType(), $this->_resource->getUserType());

        // test preset user type getting
        $userType = "Test User Type";
        $this->_resource->setUserType($userType);
        $this->assertEquals($userType, $this->_resource->getUserType());
    }

    /**
     * Test api type setter and getter
     */
    public function testApiTypeAccessors()
    {
        // test default api type getting
        $apiTypeFromRequest = 'rest';
        $this->_request->setParam('api_type', $apiTypeFromRequest);
        $this->_resource->setRequest($this->_request);
        $this->assertEquals($apiTypeFromRequest, $this->_resource->getApiType());

        // test preset api type getting
        $apiType = "Test Api Type";
        $this->_resource->setApiType($apiType);
        $this->assertEquals($apiType, $this->_resource->getApiType());
    }

    /**
     * Test resource type setter and getter
     */
    public function testResourceTypeAccessors()
    {
        // test default resource type getting
        $resourceTypeFromRequest = 'collection';
        $this->_request->setParam('type', $resourceTypeFromRequest);
        $this->_resource->setRequest($this->_request);
        $this->assertEquals($resourceTypeFromRequest, $this->_resource->getResourceType());

        // test preset resource type getting
        $resourceType = "Test Resource Type";
        $this->_resource->setResourceType($resourceType);
        $this->assertEquals($resourceType, $this->_resource->getResourceType());
    }

    /**
     * Test response setter and getter
     */
    public function testResponseAccessors()
    {
        // test preset response getting
        $this->_resource->setResponse($this->_response);
        $this->assertInstanceOf('Mage_Api2_Model_Response', $this->_resource->getResponse());
    }

    /**
     * Test request setter and getter
     */
    public function testRequestAccessors()
    {
        // test preset request getting
        $this->_resource->setRequest($this->_request);
        $this->assertInstanceOf('Mage_Api2_Model_Request', $this->_resource->getRequest());
    }

    /**
     * Test api user setter and getter
     */
    public function testApiUserAccessors()
    {
        // test preset api user getting
        $this->_resource->setApiUser($this->_guest);
        $this->assertInstanceOf('Mage_Api2_Model_Auth_User_Abstract', $this->_resource->getApiUser());
    }

    /**
     * Test renderer setter and getter
     */
    public function testRendererAccessors()
    {
        // test default renderer getting
        $availableRenderers = (array) simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/renders.xml');
        $helperMock = $this->getHelperMockBuilder('api2')->getMock();
        $helperMock->expects($this->any())
            ->method('getResponseRenderAdapters')
            ->will($this->returnValue($availableRenderers));
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->_resource->setRequest($this->_request);
        $this->assertInstanceOf('Mage_Api2_Model_Renderer_Json', $this->_resource->getRenderer());

        // test preset renderer getting
        $rendererXml = new Mage_Api2_Model_Renderer_Xml();
        $this->_resource->setRenderer($rendererXml);
        $this->assertInstanceOf('Mage_Api2_Model_Renderer_Xml', $this->_resource->getRenderer());
    }

    /**
     * Test filter setter and getter
     */
    public function testFilterAccessors()
    {
        // test default filter getting
        $this->_resource->setRequest($this->_request);
        $this->_resource->setApiUser($this->_guest);
        $this->assertInstanceOf('Mage_Api2_Model_Acl_Filter', $this->_resource->getFilter());

        // test preset filter getting
        $filter = new Mage_Api2_Model_Acl_Filter();
        $userType = 'Test user type';
        $filter->setUserType($userType);
        $this->_resource->setFilter($filter);
        $this->assertEquals($userType, $this->_resource->getFilter()->getUserType());
    }

    /**
     * Test critical messages exception
     */
    public function testCriticalWithPredefinedMessage()
    {
        $message = Mage_Api2_Model_Resource::RESOURCE_METHOD_NOT_ALLOWED;
        $this->setExpectedException('Mage_Api2_Exception', $message, Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED);
        $this->_resource->_critical($message);
    }

    /**
     * Test critical messages exception
     */
    public function testCriticalWithUnknownMessageWithoutCode()
    {
        $message = 'Unknown error message';
        $this->setExpectedException('Mage_Api2_Exception', '', Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        $this->_resource->_critical($message);
    }

    /**
     * Test critical messages exception
     */
    public function testCriticalWithUnknownMessageWithCode()
    {
        $message = 'Unknown error message';
        $code = 101;
        $this->setExpectedException('Mage_Api2_Exception', $message, $code);
        $this->_resource->_critical($message, $code);
    }

    /**
     * Test validate
     *
     * @dataProvider providerValidate
     * @param array $data
     * @param array $required
     * @param array $valuable
     * @param bool $isExceptionExpected
     */
    public function testValidate($data, $required, $valuable, $isExceptionExpected)
    {
        if ($isExceptionExpected) {
            $this->setExpectedException('Mage_Api2_Exception',
                Mage_Api2_Model_Resource::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }
        $this->_resource->setResponse($this->_response);
        $this->_resource->_validate($data, $required, $valuable);
    }

    /**
     * Data provider for testValidate()
     *
     * @return array
     */
    public function providerValidate()
    {
        return array(
            array(array('key1' => 'value1', 'key2' => 'value2'), array('key2', 'key1'), array('key2'), false),
            array(array('key1' => 'value1', 'key2' => ''), array('key1'), array('key2'), false),
            array(array('key1' => 'value1', 'key2' => 'value2'), array('key3', 'key1'), array('key3'), true),
            array(array('key1' => 'value1', 'key2' => ''), array('key2', 'key1'), array('key2'), true),
        );
    }
}

abstract class Mage_Api2_Model_ResourceMock extends Mage_Api2_Model_Resource
{
    public function _validate(array $data, array $required = array(), array $valueable = array())
    {
        parent::_validate($data, $required, $valueable);
    }

    public function _critical($message, $code = null)
    {
        parent::_critical($message, $code);
    }
}