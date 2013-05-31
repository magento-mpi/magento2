<?php
/**
 * Unit test for Mage_Adminhtml_System_AccountController controller
 *
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'Mage/Adminhtml/controllers/System/AccountController.php';

class Mage_Adminhtml_System_AccountControllerTest extends PHPUnit_Framework_TestCase
{
    protected $_controller;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Controller_Request_Http */
    protected $_requestMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Controller_Response_Http */
    protected $_responseMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_ObjectManager */
    protected $_objectManagerMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Backend_Model_Session */
    protected $_sessionMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Backend_Helper_Data */
    protected $_helperMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Backend_Model_Auth_Session */
    protected $_authSessionMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_User_Model_User */
    protected $_userMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Locale_Validator */
    protected $_validatorMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Backend_Model_Locale_Manager */
    protected $_managerMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Translate */
    protected $_translatorMock;

    public function setUp()
    {
        $this->_requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Mage_Core_Controller_Response_Http')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'create'))
            ->getMock();
        $frontControllerMock = $this->getMockBuilder('Mage_Core_Controller_Varien_Front')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $layoutMock = $this->getMockBuilder('Mage_Core_Model_Layout_Factory')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
        $this->_helperMock = $this->getMockBuilder('Mage_Backend_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();
        $this->_sessionMock = $this->getMockBuilder('Mage_Backend_Model_Session')
            ->disableOriginalConstructor()
            ->setMethods(array('addSuccess'))
            ->getMock();

        $this->_authSessionMock = $this->getMockBuilder('Mage_Backend_Model_Auth_Session')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $this->_userMock = $this->getMockBuilder('Mage_User_Model_User')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'save', 'sendPasswordResetNotificationEmail'))
            ->getMock();

        $this->_validatorMock = $this->getMockBuilder('Mage_Core_Model_Locale_Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('isValid'))
            ->getMock();

        $this->_managerMock = $this->getMockBuilder('Mage_Backend_Model_Locale_Manager')
            ->disableOriginalConstructor()
            ->setMethods(array('switchBackendInterfaceLocale'))
            ->getMock();

        $this->_translatorMock = $this->getMockBuilder('Mage_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->setMethods(array('_getTranslatedString'))
            ->getMock();

        $arguments = array(
            'request' => $this->_requestMock,
            'response' => $this->_responseMock,
            'objectManager' => $this->_objectManagerMock,
            'frontController' => $frontControllerMock,
            'layoutFactory' => $layoutMock,
            'areaCode' => Mage_Core_Model_App_Area::AREA_ADMINHTML,
            'invokeArgs' => array(
                'translator' => $this->_translatorMock,
                'helper' => $this->_helperMock,
                'session' => $this->_sessionMock
            )
        );

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_controller = $helper->getObject('Mage_Adminhtml_System_AccountController', $arguments);
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

        $this->_userMock->expects($this->any())->method('load')->will($this->returnSelf());
        $this->_validatorMock->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo($requestParams['interface_locale']))
            ->will($this->returnValue(true));
        $this->_managerMock->expects($this->any())->method('switchBackendInterfaceLocale');

        $this->_objectManagerMock->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('Mage_Backend_Model_Auth_Session'))
            ->will($this->returnValue($this->_authSessionMock));
        $this->_objectManagerMock->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('Mage_User_Model_User'))
            ->will($this->returnValue($this->_userMock));
        $this->_objectManagerMock->expects($this->at(2))
            ->method('get')
            ->with($this->equalTo('Mage_Core_Model_Locale_Validator'))
            ->will($this->returnValue($this->_validatorMock));
        $this->_objectManagerMock->expects($this->at(3))
            ->method('get')
            ->with($this->equalTo('Mage_Backend_Model_Locale_Manager'))
            ->will($this->returnValue($this->_managerMock));

        $this->_authSessionMock->setUser($this->_userMock);
        $this->_userMock->setUserId($userId);

        $this->_userMock->expects($this->once())->method('save');
        $this->_userMock->expects($this->once())->method('sendPasswordResetNotificationEmail');

        $this->_translatorMock->expects($this->once())
            ->method('_getTranslatedString')
            ->with($this->equalTo('The account has been saved.'))
            ->will($this->returnValue('The account has been saved.'));

        $this->_requestMock->setParams($requestParams);

    }
}