<?php
/**
 * Unit test for Magento_Adminhtml_Controller_System_Account controller
 *
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Controller_System_AccountTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Adminhtml_Controller_System_Account */
    protected $_controller;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Controller_Request_Http */
    protected $_requestMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Controller_Response_Http */
    protected $_responseMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|\Magento\ObjectManager\ObjectManager */
    protected $_objectManagerMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Backend_Model_Session */
    protected $_sessionMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Backend_Helper_Data */
    protected $_helperMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Backend_Model_Auth_Session */
    protected $_authSessionMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_User_Model_User */
    protected $_userMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Locale_Validator */
    protected $_validatorMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Backend_Model_Locale_Manager */
    protected $_managerMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Translate */
    protected $_translatorMock;

    public function setUp()
    {
        $this->_requestMock = $this->getMockBuilder('Magento_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->setMethods(array('getOriginalPathInfo'))
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Magento_Core_Controller_Response_Http')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_objectManagerMock = $this->getMockBuilder('Magento\ObjectManager\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'create'))
            ->getMock();
        $frontControllerMock = $this->getMockBuilder('Magento_Core_Controller_Varien_Front')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_helperMock = $this->getMockBuilder('Magento_Backend_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();
        $this->_sessionMock = $this->getMockBuilder('Magento_Backend_Model_Session')
            ->disableOriginalConstructor()
            ->setMethods(array('addSuccess'))
            ->getMock();

        $this->_authSessionMock = $this->getMockBuilder('Magento_Backend_Model_Auth_Session')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser'))
            ->getMock();

        $this->_userMock = $this->getMockBuilder('Magento_User_Model_User')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'save', 'sendPasswordResetNotificationEmail'))
            ->getMock();

        $this->_validatorMock = $this->getMockBuilder('Magento_Core_Model_Locale_Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('isValid'))
            ->getMock();

        $this->_managerMock = $this->getMockBuilder('Magento_Backend_Model_Locale_Manager')
            ->disableOriginalConstructor()
            ->setMethods(array('switchBackendInterfaceLocale'))
            ->getMock();

        $this->_translatorMock = $this->getMockBuilder('Magento_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->setMethods(array('_canUseCache'))
            ->getMock();

        $contextArgs = array(
            'getHelper', 'getSession', 'getAuthorization', 'getTranslator', 'getObjectManager', 'getFrontController',
            'getLayoutFactory', 'getEventManager', 'getRequest', 'getResponse'
        );
        $contextMock = $this->getMockBuilder('Magento_Backend_Controller_Context')
            ->disableOriginalConstructor()
            ->setMethods($contextArgs)
            ->getMock();
        $contextMock->expects($this->any())->method('getRequest')->will($this->returnValue($this->_requestMock));
        $contextMock->expects($this->any())->method('getResponse')->will($this->returnValue($this->_responseMock));
        $contextMock->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->_objectManagerMock));
        $contextMock->expects($this->any())
            ->method('getFrontController')
            ->will($this->returnValue($frontControllerMock));

        $contextMock->expects($this->any())->method('getHelper')->will($this->returnValue($this->_helperMock));
        $contextMock->expects($this->any())->method('getSession')->will($this->returnValue($this->_sessionMock));
        $contextMock->expects($this->any())->method('getTranslator')->will($this->returnValue($this->_translatorMock));

        $args = array('context' => $contextMock);

        $testHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_controller = $testHelper->getObject('Magento_Adminhtml_Controller_System_Account', $args);
    }

    public function testSaveAction()
    {
        $userId = 1;
        $requestParams = array(
            'password' => 'password',
            'password_confirmation' => true,
            'interface_locale' => 'US',
            'username' => 'Foo',
            'firstname' => 'Bar',
            'lastname' => 'Dummy',
            'email' => 'test@example.com'
        );

        $testedMessage = 'The account has been saved.';

        $this->_authSessionMock->expects($this->any())->method('getUser')->will($this->returnValue($this->_userMock));

        $this->_userMock->expects($this->any())->method('load')->will($this->returnSelf());
        $this->_validatorMock->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo($requestParams['interface_locale']))
            ->will($this->returnValue(true));
        $this->_managerMock->expects($this->any())->method('switchBackendInterfaceLocale');

        $this->_objectManagerMock->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Magento_Backend_Model_Auth_Session'))
            ->will($this->returnValue($this->_authSessionMock));
        $this->_objectManagerMock->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('Magento_User_Model_User'))
            ->will($this->returnValue($this->_userMock));
        $this->_objectManagerMock->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('Magento_Core_Model_Locale_Validator'))
            ->will($this->returnValue($this->_validatorMock));
        $this->_objectManagerMock->expects($this->at(3))
            ->method('get')
            ->with($this->equalTo('Magento_Backend_Model_Locale_Manager'))
            ->will($this->returnValue($this->_managerMock));

        $this->_userMock->setUserId($userId);

        $this->_userMock->expects($this->once())->method('save');
        $this->_userMock->expects($this->once())->method('sendPasswordResetNotificationEmail');

        $this->_requestMock->setParams($requestParams);

        $this->_sessionMock->expects($this->once())->method('addSuccess')->with($this->equalTo($testedMessage));

        $this->_controller->saveAction();
    }
}
