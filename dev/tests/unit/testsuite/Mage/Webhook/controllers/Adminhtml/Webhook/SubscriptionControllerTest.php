<?php
/**
 * Mage_Webhook_Adminhtml_Webhook_SubscriptionController
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'Mage/Webhook/controllers/Adminhtml/Webhook/SubscriptionController.php';

class Mage_Webhook_Adminhtml_Webhook_SubscriptionControllerTest extends PHPUnit_Framework_TestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockObjectManager;

    /** @var Mage_Webhook_Adminhtml_Webhook_SubscriptionController */
    protected $_subscriptionController;

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
     * @param null $subscriptionService
     * @param null $registry
     * @return object
     */
    protected function _createSubscriptionController($request = null, $subscriptionService = null, $registry = null)
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

        // for _setActiveMenu
        $blockMock = $this->getMockBuilder('Mage_Backend_Block_Menu')
            ->disableOriginalConstructor()
            ->getMock();
        $menuMock = $this->getMockBuilder('Mage_Backend_Model_Menu')
            ->disableOriginalConstructor()
            ->getMock();
        $menuMock->expects($this->any())->method('getParentItems')->will($this->returnValue(array()));
        $blockMock->expects($this->any())->method('getMenuModel')->will($this->returnValue($menuMock));

        $layoutMock->expects($this->any())->method('getMessagesBlock')->will($this->returnValue($blockMock));
        $layoutMock->expects($this->any())->method('getBlock')->will($this->returnValue($blockMock));


        $layoutFactoryMock->expects($this->any())->method('createLayout')->will($this->returnValue($layoutMock));

        $constructorParameters = array(
            'layoutFactory' => $layoutFactoryMock,
            'objectManager' => $this->_mockObjectManager
        );

        if (isset($request)) {
            $constructorParameters['request'] = $request;
        }
        if (isset($subscriptionService)) {
            $constructorParameters['subscriptionService'] = $subscriptionService;
        }
        if (isset($registry)) {
            $constructorParameters['registry'] = $registry;
        }

        /** Create SubscriptionController to test */
        $subscriptionController = $this->_objectManagerHelper
            ->getObject('Mage_Webhook_Adminhtml_Webhook_SubscriptionController',
                $constructorParameters);

        return $subscriptionController;
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

        $this->_subscriptionController = $this->_createSubscriptionController();
    }

    /**
     * Common mock 'expect' pattern.
     * Calls that need to be mocked out when
     * Mage_Backend_Controller_ActionAbstract loadLayout() and renderLayout() are called.
     */
    protected function _verifyLoadAndRenderLayout() {
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

    public function testIndexAction()
    {
        // loadLayout
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_App')->will($this->returnValue($this->_mockApp));
        $this->_mockObjectManager->expects($this->at(1))->method('get')
            ->with('Mage_Core_Model_Event_Manager')->will($this->returnValue($this->_mockEventManager));

        // translate title
        $this->_mockObjectManager->expects($this->at(2))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
        $this->_mockObjectManager->expects($this->at(3))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
        $this->_mockObjectManager->expects($this->at(4))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        // renderLayout
        $this->_mockObjectManager->expects($this->at(5))->method('get')
            ->with('Mage_Core_Model_Event_Manager')->will($this->returnValue($this->_mockEventManager));
        $this->_mockObjectManager->expects($this->at(6))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $this->_subscriptionController->indexAction();

    }

    public function testNewAction()
    {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->getMock();
        $requestMock->expects($this->any())->method('setActionName')
            ->will( $this->returnValue($requestMock));

        $subscriptionController = $this->_createSubscriptionController($requestMock);
        $subscriptionController->newAction();
    }

    public function testEditActionHasData()
    {
        // put data in session, the magic function getFormData is called so, must match __call method name
        $mockSession = Mage::getSingleton('Mage_Backend_Model_Session');
        $mockSession->expects($this->any())->method('__call')->will($this->returnValue(array('testkey' =>'testvalue')));

        // loadLayout
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_App')->will($this->returnValue($this->_mockApp));
        $this->_mockObjectManager->expects($this->at(1))->method('get')
            ->with('Mage_Core_Model_Event_Manager')->will($this->returnValue($this->_mockEventManager));

        // translate title
        $this->_mockObjectManager->expects($this->at(2))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
        $this->_mockObjectManager->expects($this->at(3))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
        $this->_mockObjectManager->expects($this->at(4))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $this->_mockObjectManager->expects($this->at(5))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        // renderLayout
        $this->_mockObjectManager->expects($this->at(6))->method('get')
            ->with('Mage_Core_Model_Event_Manager')->will($this->returnValue($this->_mockEventManager));
        $this->_mockObjectManager->expects($this->at(7))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $subscriptionController = $this->_createSubscriptionController(null, null);
        $subscriptionController->editAction();
    }

    public function testEditActionNoDataAdd()
    {
        // Set the registry object to return 'new' so the 'Add Subscription' path is followed
        $registryMock = $this->getMockBuilder('Mage_Core_Model_Registry')->disableOriginalConstructor()->getMock();
        $registryMock->expects($this->any())->method('registry')->will($this->returnValue('new'));

        // loadLayout
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_App')->will($this->returnValue($this->_mockApp));
        $this->_mockObjectManager->expects($this->at(1))->method('get')
            ->with('Mage_Core_Model_Event_Manager')->will($this->returnValue($this->_mockEventManager));

        // translate title
        $this->_mockObjectManager->expects($this->at(2))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
        $this->_mockObjectManager->expects($this->at(3))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
        $this->_mockObjectManager->expects($this->at(4))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $this->_mockObjectManager->expects($this->at(5))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        // renderLayout
        $this->_mockObjectManager->expects($this->at(6))->method('get')
            ->with('Mage_Core_Model_Event_Manager')->will($this->returnValue($this->_mockEventManager));
        $this->_mockObjectManager->expects($this->at(7))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $subscriptionController = $this->_createSubscriptionController(null, null, $registryMock);
        $subscriptionController->editAction();
    }

    public function testEditException()
    {

        // have load layout throw an exception
        // loadLayout
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_App')->will($this->throwException(new Mage_Core_Exception()));
        $this->_subscriptionController->editAction();

    }

    public function testSaveAction()
    {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->getMock();
        $requestMock->expects($this->any())->method('getPost')->will($this->returnValue(array('apikey' => 'abc')));
        $requestMock->expects($this->any())->method('getParam')->will($this->returnValue('1'));

        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $subscriptionServiceMock->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // translate success message
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $subscriptionController = $this->_createSubscriptionController($requestMock, $subscriptionServiceMock);
        $subscriptionController->saveAction();
    }

    public function testSaveActionNoData()
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

        // translates the error
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $subscriptionController = $this->_createSubscriptionController($requestMock);
        $subscriptionController->saveAction();
    }

    public function testSaveActionException()
    {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->getMock();
        $requestMock->expects($this->any())->method('getParam')->will($this->returnValue('1'));

        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        // Have subscription service throw an exception to test exception path
        $subscriptionServiceMock->expects($this->any())
            ->method('get')
            ->with(1)
            ->will($this->throwException(new Mage_Core_Exception()));

        $subscriptionController = $this->_createSubscriptionController($requestMock, $subscriptionServiceMock);
        $subscriptionController->saveAction();
    }

    public function testSaveActionNew()
    {
        // Set the registry object to return 'new' so the 'create' path is followed
        $registryMock = $this->getMockBuilder('Mage_Core_Model_Registry')->disableOriginalConstructor()->getMock();
        $registryMock->expects($this->any())->method('registry')->will($this->returnValue('new'));

        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->getMock();
        $requestMock->expects($this->any())->method('getPost')->will($this->returnValue(array('apikey' => 'abc')));
        $requestMock->expects($this->any())->method('getParam')->will($this->returnValue('1'));

        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $subscriptionServiceMock->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // translate success message
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $subscriptionController = $this->_createSubscriptionController(
            $requestMock, $subscriptionServiceMock, $registryMock);
        $subscriptionController->saveAction();
    }

    /**
     * Test Save when action is not new, but there is no ID
     */
    public function testSaveActionNoId()
    {
        // Set the registry object to return 'new' so the 'create' path is followed
        $registryMock = $this->getMockBuilder('Mage_Core_Model_Registry')->disableOriginalConstructor()->getMock();
        $registryMock->expects($this->any())->method('registry')->will($this->returnValue('old'));

        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->getMock();
        $requestMock->expects($this->any())->method('getPost')
            ->will($this->returnValue(array('apikey' => 'abc', 'name' => 'testSubcription')));

        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();

        // translate success message
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $subscriptionController = $this->_createSubscriptionController(
            $requestMock, $subscriptionServiceMock, $registryMock);
        $subscriptionController->saveAction();
    }

    /**
     * Test deleteAction when subscription is an alias, not created by user.
     */
    public function testDeleteActionAlias()
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

        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $subscriptionServiceMock->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'),
                       'alias' => 'true'
                )
            ));

        // translate success message
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $subscriptionController = $this->_createSubscriptionController($requestMock, $subscriptionServiceMock);
        $subscriptionController->deleteAction();

    }

    public function testDeleteAction()
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

        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $subscriptionServiceMock->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // translate success message
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $subscriptionController = $this->_createSubscriptionController($requestMock, $subscriptionServiceMock);
        $subscriptionController->deleteAction();

    }

    public function testDeleteActionException ()
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

        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $subscriptionServiceMock->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // Have subscription service throw an exception to go down exception path
        $subscriptionServiceMock->expects($this->any())
            ->method('delete')
            ->will($this->throwException(new Mage_Core_Exception()));

        $subscriptionController = $this->_createSubscriptionController($requestMock, $subscriptionServiceMock);
        $subscriptionController->deleteAction();
    }

    public function testRevokeAction()
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

        // translate success message
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $subscriptionController = $this->_createSubscriptionController($requestMock);
        $subscriptionController->revokeAction();
    }

    public function testRevokeActionNoData()
    {
        // translate error message
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $this->_subscriptionController->revokeAction();
    }

    public function testRevokeActionException()
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

        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();

        // Have subscription service throw an exception to go down exception path
        $subscriptionServiceMock->expects($this->any())
            ->method('revoke')
            ->will($this->throwException(new Mage_Core_Exception()));

        $subscriptionController = $this->_createSubscriptionController($requestMock, $subscriptionServiceMock);
        $subscriptionController->revokeAction();
    }

    public function testActivateAction()
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

        // translate success message
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));

        $subscriptionController = $this->_createSubscriptionController($requestMock);
        $subscriptionController->activateAction();
    }

    public function testActivateActionNoData()
    {
        // translate error message
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
        $this->_subscriptionController->activateAction();
    }

    public function testActivateActionException()
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

        $subscriptionServiceMock = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();

        // Have subscription service throw an exception to go down exception path
        $subscriptionServiceMock->expects($this->any())
            ->method('activate')
            ->will($this->throwException(new Mage_Core_Exception()));

        $subscriptionController = $this->_createSubscriptionController($requestMock, $subscriptionServiceMock);
        $subscriptionController->activateAction();
    }
}
