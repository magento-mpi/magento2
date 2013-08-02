<?php
/**
 * Mage_Webhook_Adminhtml_Webhook_RegistrationController
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'Mage/Webhook/controllers/Adminhtml/Webhook/RegistrationController.php';

class Mage_Webhook_Adminhtml_Webhook_RegistrationControllerTest extends PHPUnit_Framework_TestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockObjectManager;

    /** @var Mage_Webhook_Adminhtml_Webhook_RegistrationController */
    protected $_registrationController;

    /** @var Magento_Test_Helper_ObjectManager $objectManagerHelper */
    protected $_objectManagerHelper;

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    protected $_mockApp;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockConfig;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockEventManager;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockTranslateModel;

    /**
     * Makes sure that Mage has a mock object manager set, and returns that instance.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _setMageObjectManager()
    {
        Mage::reset();
        $this->_mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        Mage::setObjectManager($this->_mockObjectManager);

    }

    /**
     * Creates the RegistrationController to test.
     * Parameters are to override default empty mock objects created by test object manager helper.
     * @param null $request
     * @param null $userService
     * @param null $subscriptionService
     * @return object
     */
    protected function _createRegistrationController($request = null, $userService = null, $subscriptionService = null)
    {
        // Mock Layout factory passed into constructor
        $layoutFactoryMock = $this->getMockBuilder('Mage_Core_Model_Layout_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock = $this->getMockBuilder('Mage_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMergeMock = $this->getMockBuilder('Mage_Core_Model_Layout_Merge')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->any())->method('getUpdate')->will($this->returnValue($layoutMergeMock));
        $testElement = new Varien_Simplexml_Element('<test>test</test>');
        $layoutMock->expects($this->any())->method('getNode')->will($this->returnValue($testElement));
        $blockMock = $this->getMockBuilder('Mage_Core_Block_Abstract')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->any())->method('getMessagesBlock')->will($this->returnValue($blockMock));
        $layoutFactoryMock->expects($this->any())->method('createLayout')->will($this->returnValue($layoutMock));

        $constructorParameters = array(
            'layoutFactory' => $layoutFactoryMock,
            'objectManager' => $this->_mockObjectManager
        );

        if (isset($request)) {
            $constructorParameters['request'] = $request;
        }
        if (isset($userService)) {
            $constructorParameters['userService'] = $userService;
        }
        if (isset($subscriptionService)) {
            $constructorParameters['subscriptionService'] = $subscriptionService;
        }

        /** Create RegistrationController to test */
        $registrationController = $this->_objectManagerHelper
            ->getObject('Mage_Webhook_Adminhtml_Webhook_RegistrationController',
                $constructorParameters);

        return $registrationController;
    }

    public function setUp()
    {
        /** @var Magento_Test_Helper_ObjectManager $objectManagerHelper */
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_setMageObjectManager();

        $mockBackendHelperData = $this->getMockBuilder('Mage_Backend_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();
        Mage::register('_helper/Mage_Backend_Helper_Data', $mockBackendHelperData);

        $mockBackendModelSession = $this->getMockBuilder('Mage_Backend_Model_Session')->getMock();
        Mage::register('_singleton/Mage_Backend_Model_Session', $mockBackendModelSession);

        $mockAuth = $this->getMockBuilder('Mage_Core_Model_Authorization')
            ->disableOriginalConstructor()
            ->getMock();
        Mage::register('_singleton/Mage_Core_Model_Authorization', $mockAuth);

        // Initialize mocks which are used in several testcases
        $this->_mockApp = $this->getMockBuilder('Mage_Core_Model_App')
            ->setMethods( array('getConfig'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockConfig = $this->getMockBuilder('Mage_Core_Model_Config')->disableOriginalConstructor()->getMock();
        $this->_mockApp->expects($this->any())->method('getConfig')->will($this->returnValue($this->_mockConfig));
        $this->_mockEventManager = $this->getMockBuilder('Mage_Core_Model_Event_Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockTranslateModel = $this->getMockBuilder('Mage_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_registrationController = $this->_createRegistrationController();
    }

    /**
     * Common mock 'expect' pattern.
     * Calls that need to be mocked out when
     * Mage_Backend_Controller_ActionAbstract loadLayout() and renderLayout() are called.
     */
    protected function verifyLoadAndRenderLayout() {
        // loadLayout
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_App')->will($this->returnValue($this->_mockApp));
        $this->_mockObjectManager->expects($this->at(1))->method('get')
            ->with('Mage_Core_Model_Event_Manager')->will($this->returnValue($this->_mockEventManager));
        // renderLayout
        $this->_mockObjectManager->expects($this->at(2))->method('get')
            ->with('Mage_Core_Model_Event_Manager')->will($this->returnValue($this->_mockEventManager));
        $this->_mockObjectManager->expects($this->at(3))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
    }

    public function testActivateActionNotSubscribed()
    {
        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $subscriptionServiceMock->expects($this->any())->method('get')
            ->will($this->throwException(new Mage_Core_Exception()));
        $registrationController = $this->_createRegistrationController(null, null, $subscriptionServiceMock);
        $registrationController->activateAction();
    }

    public function testActivateAction()
    {
        $this->verifyLoadAndRenderLayout();
        $this->_registrationController->activateAction();
    }

    public function testAcceptAction()
    {
        $this->_registrationController->acceptAction();
    }

    public function testAcceptActionNotSubscribed()
    {
        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $subscriptionServiceMock->expects($this->any())->method('get')
            ->will($this->throwException(new Mage_Core_Exception()));
        $registrationController = $this->_createRegistrationController(null, null, $subscriptionServiceMock);
        $registrationController->acceptAction();
    }

    public function testUserAction()
    {
        $this->verifyLoadAndRenderLayout();
        $this->_registrationController->userAction();
    }

    public function testUserActionNotSubscribed()
    {
        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $subscriptionServiceMock->expects($this->any())->method('get')
            ->will($this->throwException(new Mage_Core_Exception()));
        $registrationController = $this->_createRegistrationController(null, null, $subscriptionServiceMock);
        $registrationController->userAction();
    }

    public function testRegisterActionNoData()
    {
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
        $this->_registrationController->registerAction();
    }

    public function testRegisterActionInvalidEmail()
    {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->getMock();
        $requestMock->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                    array(
                         array('id', null, '1'),
                         array('apikey', null, '2'),
                         array('apisecret', null, 'secret'),
                         array('email', null, 'invalid.email.example.com'),
                         array('company', null, 'Example')
                    )
                ));

        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
        $registrationController = $this->_createRegistrationController($requestMock);
        $registrationController->registerAction();
    }

    public function testRegisterAction()
    {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->getMock();
        $requestMock->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                array(
                    array('id', null, '1'),
                    array('apikey', null, '2'),
                    array('apisecret', null, 'secret'),
                    array('email', null, 'test@example.com'),
                    array('company', null, 'Example')
                )
            ));

        $userServiceMock = $this->getMockBuilder('Mage_Webhook_Service_UserV1')
            ->disableOriginalConstructor()
            ->getMock();

        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $subscriptionServiceMock->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));
        $subscriptionServiceMock->expects($this->any())->method('update')->will($this->returnArgument(0));

        $registrationController = $this->_createRegistrationController($requestMock, $userServiceMock,
            $subscriptionServiceMock);

        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $registrationController->registerAction();
    }

    public function testFailedAction()
    {
        $this->verifyLoadAndRenderLayout();
        $this->_registrationController->failedAction();
    }

    public function testSucceededAction()
    {
        $this->verifyLoadAndRenderLayout();
        $this->_mockObjectManager->expects($this->at(4))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $this->_registrationController->succeededAction();
    }

    public function testSucceededActionNotSubscribed()
    {
        $this->verifyLoadAndRenderLayout();
        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $subscriptionServiceMock->expects($this->any())->method('get')
            ->will($this->throwException(new Mage_Core_Exception()));
        $registrationController = $this->_createRegistrationController(null, null, $subscriptionServiceMock);
        $registrationController->succeededAction();
    }
}
