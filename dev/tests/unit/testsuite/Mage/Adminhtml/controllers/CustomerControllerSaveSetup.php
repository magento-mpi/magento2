<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once realpath(__DIR__) . '/CustomerControllerSaveBaseSetup.php';

class Mage_Adminhtml_CustomerControllerSaveSetup extends Mage_Adminhtml_CustomerControllerSaveBaseSetup
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

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
    protected $_registryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclMock;

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

        /** Initialize translator mock */
        $this->_initializeTranslatorMock();

        /** Initialize front controller mock */
        $this->_initializeFrontControllerMock();

        /** Initialize controller instance */
        $this->_initializeControllerInstance();
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

    protected function _initializeEventManagerMock()
    {
        $this->_eventManagerMock = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false);
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

    protected function _getObjectFactoryMap()
    {
        $objectFactoryMap = array(
            array('Mage_Customer_Model_Customer', array(), $this->_customerMock),
            array('Mage_Customer_Model_Form', array(), $this->_customerFromMock),
        );
        return $objectFactoryMap;
    }

    protected function _initializeObjectFactoryMock()
    {
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_objectFactoryMock->expects($this->any())
            ->method('getModelInstance')->will($this->returnValueMap($this->_getObjectFactoryMap()));
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

    protected function _initializeRegistryMock()
    {
        $this->_registryMock = $this->getMock('Mage_Core_Model_Registry', array(), array(), '', false);
        $this->_registryMock->expects($this->once())
            ->method('registry')->with('current_customer')->will($this->returnValue($this->_customerMock));
    }

    protected function _initializeAclMock()
    {
        $this->_aclMock = $this->getMock('Mage_Backend_Model_Auth_Session', array(), array(), '', false);
    }

    protected function _getInvokeArgs()
    {
        $invokeArgs = array(
            'areaCode' => 'adminhtml',
            'objectFactory' => $this->_objectFactoryMock,
            'eventManager' => $this->_eventManagerMock,
            'session' => $this->_sessionMock,
            'helper' => $this->_helperMock,
            'registry' => $this->_registryMock,
            'acl' => $this->_aclMock,
            'frontController' => $this->_frontCtrlMock,
            'translator' => $this->_translatorMock,
        );
        return $invokeArgs;
    }

    public function tearDown()
    {
        unset($this->_objectFactoryMock);
        unset($this->_sessionMock);
        unset($this->_customerFromMock);
        unset($this->_customerMock);
        unset($this->_eventManagerMock);
        unset($this->_registryMock);
        unset($this->_aclMock);
    }

    protected function _prepareMocksForSuccessCustomerSave()
    {
        $this->_sessionMock->expects($this->once())->method('addSuccess')->with('The customer has been saved.');

        $this->_helperMock->expects($this->once())
            ->method('__')->with('The customer has been saved.')->will($this->returnArgument(0));

        $eventParams = array(
            'customer' => $this->_customerMock,
            'request' => $this->_requestMock
        );
        $this->_eventManagerMock->expects($this->at(0))
            ->method('dispatch')->with('adminhtml_customer_prepare_save', $eventParams);
        $this->_eventManagerMock->expects($this->at(1))
            ->method('dispatch')->with('adminhtml_customer_save_after', $eventParams);

        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL)->will($this->returnValue(true));

        $this->_customerMock->expects($this->once())->method('save');
    }
}
