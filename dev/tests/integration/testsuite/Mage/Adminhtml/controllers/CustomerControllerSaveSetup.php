<?php
require_once realpath(__DIR__ . '/../../../../../../../')
    . '/app/code/core/Mage/Adminhtml/controllers/CustomerController.php';
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_CustomerControllerSaveSetup extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_CustomerController
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customerFromMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclMock;

    /**
     * Test customer id
     * @var int
     */
    protected $_customerId;

    /**
     * @var array
     */
    protected $_formData;

    /**
     * @var array
     */
    protected $_postData;

    public function setUp()
    {
        $this->_customerId = 1;

        $this->_initializeFormData();

        $this->_initializePostData();

        /** Initialize request mock */
        $this->_initializeRequestMock();

        /** Initialize response mock */
        $this->_initializeResponseMock();

        /** Initialize customer mock  */
        $this->_initializeCustomerMock();

        /** Initialize event manager mock  */
        $this->_initializeEventManagerMock();

        /** Initialize customer form mock  */
        $this->_initializeCustomerFormMock();

        /** Initialize object factory mock */
        $this->_initializeObjectFactoryMock();

        /** Initialize session mock */
        $this->_initializeSessionMock();

        /** Initialize helper mock */
        $this->_initializeHelperMock();

        /** Initialize register manager mock */
        $this->_initializeRegistryMock();

        /** Initialize register manager mock */
        $this->_initializeAclMock();

        /** Initialize controller instance */
        $this->_initializeControllerInstance();
    }

    protected function _initializeRegistryMock()
    {
        $this->_registryMock = $this->getMock('Mage_Core_Model_Registry', array(), array(), '', false);
        $this->_registryMock->expects($this->once())
            ->method('registry')->with('current_customer')->will($this->returnValue($this->_customerMock));
    }

    protected function _initializeCustomerFormMock()
    {
        $this->_customerFromMock = $this->getMock('Mage_Customer_Model_Form',
            array('setEntity', 'setFormCode', 'ignoreInvisible', 'extractData', 'validateData', 'compactData'),
            array(),
            '',
            false,
            false
        );

        $this->_customerFromMock->expects($this->once())
            ->method('setFormCode')->with('adminhtml_customer')->will($this->returnSelf());

        $this->_customerFromMock->expects($this->once())
            ->method('ignoreInvisible')->with(false)->will($this->returnSelf());

        $this->_customerFromMock->expects($this->once())
            ->method('extractData')->with($this->_requestMock, 'account')->will($this->returnValue($this->_formData));
    }

    protected function _initializeControllerInstance()
    {
        $this->_model = new Mage_Adminhtml_CustomerController(
            $this->_requestMock,
            $this->_responseMock,
            $this->_getInvokeArgs()
        );
    }

    protected function _getInvokeArgs()
    {
        $invokeArgs = array(
            'areaCode' => Mage::helper('Mage_Backend_Helper_Data')->getAreaCode(),
            'objectFactory' => $this->_objectFactoryMock,
            'eventManager' => $this->_eventManagerMock,
            'session' => $this->_sessionMock,
            'helper' => $this->_helperMock,
            'registry' => $this->_registryMock,
            'acl' => $this->_aclMock
        );
        return $invokeArgs;
    }

    protected function _initializeAclMock()
    {
        $this->_aclMock = $this->getMock('Mage_Backend_Model_Auth_Session', array(), array(), '', false);
    }

    protected function _initializeHelperMock()
    {
        $this->_helperMock = $this->getMock('Mage_Backend_Helper_Data', array('getUrl', '__'), array(), '', false);
        $this->_helperMock->expects($this->once())->method('getUrl')->will($this->returnArgument(0));
    }

    protected function _initializeSessionMock()
    {
        $this->_sessionMock = $this->getMock('Mage_Backend_Model_Session',
            array('addError', 'setCustomerData', 'isAllowed', 'addSuccess', 'setIsUrlNotice', 'addException'),
            array(),
            '',
            false,
            false
        );
    }

    protected function _initializeObjectFactoryMock()
    {
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_objectFactoryMock->expects($this->any())
            ->method('getModelInstance')->will($this->returnValueMap($this->_getObjectFactoryMap()));
    }

    protected function _getObjectFactoryMap()
    {
        $objectFactoryMap = array(
            array('Mage_Customer_Model_Customer', array(), $this->_customerMock),
            array('Mage_Customer_Model_Form', array(), $this->_customerFromMock),
        );
        return $objectFactoryMap;
    }

    protected function _initializeEventManagerMock()
    {
        $this->_eventManagerMock = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false);
    }

    protected function _initializeResponseMock()
    {
        $this->_responseMock = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false, false);
        $this->_responseMock->expects($this->once())->method('setRedirect')->with($this->stringEndsWith('edit'));
    }

    protected function _initializeRequestMock()
    {
        $requestParamMap = array(
            array('back', false, true),
            array('customer_id', null, $this->_customerId),
        );
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $this->_requestMock->expects($this->once())->method('getPost')->will($this->returnValue($this->_postData));
        $this->_requestMock->expects($this->any())->method('getParam')->will($this->returnValueMap($requestParamMap));
    }

    protected function _initializeCustomerMock()
    {
        $this->_customerMock = $this->getMock('Mage_Customer_Model_Customer',
            array('load', 'getId', 'save', 'getAddressesCollection', 'getConfirmation', 'isObjectNew', 'getWebsiteId',
                'setIsSubscribed', 'setSendemailStoreId', 'setPassword', 'setForceConfirmed', 'generatePassword',
                'getPassword', 'sendNewAccountEmail', 'getSendemailStoreId', 'changePassword',
                'sendPasswordReminderEmail', 'getAddressItemById'
            ),
            array(),
            '',
            false,
            false
        );
        $this->_customerMock->expects($this->once())->method('load')->with($this->equalTo($this->_customerId));
        $this->_customerMock->expects($this->any())->method('getId')->will($this->returnValue($this->_customerId));
    }

    protected function _initializePostData()
    {
        $this->_postData = array(
            'account' => $this->_formData,
            'subscription' => array(),
            'address' => array(
                '_template_' => 'test template',
            )
        );
    }

    protected function _initializeFormData()
    {
        $this->_formData = array(
            'website_id' => 0,
            'group_id' => 1,
        );
    }

    public function tearDown()
    {
        unset($this->_responseMock);
        unset($this->_requestMock);
        unset($this->_objectFactoryMock);
        unset($this->_sessionMock);
        unset($this->_customerFromMock);
        unset($this->_customerMock);
        unset($this->_model);
        unset($this->_eventManagerMock);
        unset($this->_helperMock);
        unset($this->_registryMock);
        unset($this->_aclMock);
    }
}
