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
    protected $_mockLayoutFilter;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockConfig;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockEventManager;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockTranslateModel;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockBackendModelSession;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockBackendControllerContext;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockSubscriptionService;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockRegistry;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockRequest;

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
     * Creates the SubscriptionController to test.
     * @return object
     */
    protected function _createSubscriptionController()
    {
        // Mock Layout passed into constructor
        $layoutMock = $this->getMockBuilder('Mage_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMergeMock = $this->getMockBuilder('Mage_Core_Model_Layout_Merge')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->any())->method('getUpdate')->will($this->returnValue($layoutMergeMock));
        $testElement = new Magento_Simplexml_Element('<test>test</test>');
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

        $contextParameters = array(
            'layout' => $layoutMock,
            'objectManager' => $this->_mockObjectManager,
            'session' => $this->_mockBackendModelSession,
            'translator' => $this->_mockTranslateModel,
            'request' => $this->_mockRequest,
        );

        $this->_mockBackendControllerContext = $this->_objectManagerHelper
            ->getObject('Mage_Backend_Controller_Context',
                $contextParameters);

        $subControllerParams = array(
            'context' => $this->_mockBackendControllerContext,
            'subscriptionService' => $this->_mockSubscriptionService,
            'registry' => $this->_mockRegistry,
        );

        /** Create SubscriptionController to test */
        $subscriptionController = $this->_objectManagerHelper
            ->getObject('Mage_Webhook_Adminhtml_Webhook_SubscriptionController',
                $subControllerParams);
        return $subscriptionController;
    }

    /**
     * Setup object manager and initialize mocks
     */
    public function setUp()
    {
        /** @var Magento_Test_Helper_ObjectManager $objectManagerHelper */
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_setMageObjectManager();

        $mockBackendModelSession = $this->getMockBuilder('Mage_Backend_Model_Session')->getMock();
        Mage::register('_singleton/Mage_Backend_Model_Session', $mockBackendModelSession);

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
        $this->_mockLayoutFilter = $this->getMockBuilder('Mage_Core_Model_Layout_Filter_Acl')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockBackendModelSession = $this->getMockBuilder('Mage_Backend_Model_Session')
            ->getMock();

        $this->_mockTranslateModel = $this->getMockBuilder('Mage_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockSubscriptionService = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockRequest = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->getMock();
        $this->_mockRegistry = $this->getMockBuilder('Mage_Core_Model_Registry')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Common mock 'expect' pattern.
     * Calls that need to be mocked out when
     * Mage_Backend_Controller_ActionAbstract loadLayout() and renderLayout() are called.
     */
    protected function _verifyLoadAndRenderLayout() {
        // loadLayout
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Config')->will($this->returnValue($this->_mockConfig));
        $this->_mockObjectManager->expects($this->at(1))->method('get')
            ->with('Mage_Core_Model_Layout_Filter_Acl')->will($this->returnValue($this->_mockLayoutFilter));

        // renderLayout
        $this->_mockObjectManager->expects($this->at(2))->method('get')
            ->with('Mage_Backend_Model_Session')->will($this->returnValue($this->_mockBackendModelSession));
        $this->_mockObjectManager->expects($this->at(3))->method('get')
            ->with('Mage_Core_Model_Translate')->will($this->returnValue($this->_mockTranslateModel));
    }

    public function testIndexAction()
    {
        $this->_verifyLoadAndRenderLayout();

        // Verify title
        $this->_mockTranslateModel->expects($this->at(0))
            ->method('translate')
            ->with($this->equalTo(
                    array( new Mage_Core_Model_Translate_Expr('System'))));
        $this->_mockTranslateModel->expects($this->at(1))
            ->method('translate')
            ->with($this->equalTo(
                    array( new Mage_Core_Model_Translate_Expr('Web Services'))));
        $this->_mockTranslateModel->expects($this->at(2))
            ->method('translate')
            ->with($this->equalTo(
                    array( new Mage_Core_Model_Translate_Expr('WebHook Subscriptions'))));

        // renderLayout
        $this->_subscriptionController = $this->_createSubscriptionController();
        $this->_subscriptionController->indexAction();
    }

    public function testNewAction()
    {
       // verify the request is forwarded to 'edit' action
        $this->_mockRequest->expects($this->any())->method('setActionName')->with('edit')
            ->will( $this->returnValue($this->_mockRequest));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->newAction();
    }

    public function testEditActionHasData()
    {
        // put data in session, the magic function getFormData is called so, must match __call method name
        $this->_mockBackendModelSession = Mage::getSingleton('Mage_Backend_Model_Session');
        $this->_mockBackendModelSession->expects($this->any())->method('__call')->will($this->returnValue(array('testkey' =>'testvalue')));

        $this->_verifyLoadAndRenderLayout();

        // verify title is 'Edit Subscription'
        $expected = new Mage_Core_Model_Translate_Expr('Edit Subscription');
        $this->_mockTranslateModel->expects($this->at(3))
            ->method('translate')
            ->with($this->equalTo(
                    array( $expected)));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->editAction();
    }

    public function testEditActionNoDataAdd()
    {
        // Set the registry object to return 'new' so the 'Add Subscription' path is followed
        $this->_mockRegistry->expects($this->any())->method('registry')->will($this->returnValue('new'));

        $this->_verifyLoadAndRenderLayout();

        // verify title is 'Add Subscription'
        $expected = new Mage_Core_Model_Translate_Expr('Add Subscription');
        $this->_mockTranslateModel->expects($this->at(3))
            ->method('translate')
            ->with($this->equalTo(
                    array( $expected)));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->editAction();
    }

    public function testEditException()
    {
        $exceptionMessage = 'An exception happened';
        // have load layout throw an exception
        $this->_mockObjectManager->expects($this->at(0))->method('get')
            ->with('Mage_Core_Model_Config')->will($this->throwException(new Mage_Core_Exception($exceptionMessage)));

        // verify the error
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo($exceptionMessage));

        $this->_subscriptionController = $this->_createSubscriptionController();
        $this->_subscriptionController->editAction();
    }

    public function testSaveAction()
    {
        // Use real translate model
        $this->_mockTranslateModel = null;

        $this->_mockRequest->expects($this->any())->method('getPost')->will($this->returnValue(array('apikey' => 'abc')));
        $this->_mockRequest->expects($this->any())->method('getParam')->will($this->returnValue('1'));

        $this->_mockSubscriptionService->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // verify success message
        $this->_mockBackendModelSession->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been saved.'));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->saveAction();
    }

    public function testSaveActionNoData()
    {
        // Use real translate model
        $this->_mockTranslateModel = null;

        $this->_mockRequest->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                    array(
                         array('id', null, '1'),
                         array('apikey', null, '2'),
                         array('apisecret', null, 'secret'),
                         array('email', null, 'invalid.email.example.com'),
                         array('company', null, 'Example')
                    )
                ));

        // verify the error
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo('The subscription \'\' has not been saved, as no data was provided.'));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->saveAction();
    }

    public function testSaveActionException()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')->will($this->returnValue('1'));

        // Have subscription service throw an exception to test exception path
        $exceptionMessage = 'an exception happened';
        $this->_mockSubscriptionService->expects($this->any())
            ->method('get')
            ->with(1)
            ->will($this->throwException(new Mage_Core_Exception($exceptionMessage)));

        // Verify error
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo($exceptionMessage));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->saveAction();
    }

    public function testSaveActionNew()
    {
        // Set the registry object to return 'new' so the 'create' path is followed
        $this->_mockRegistry->expects($this->any())->method('registry')->will($this->returnValue('new'));

        $this->_mockRequest->expects($this->any())->method('getPost')
            ->will($this->returnValue(array('apikey' => 'abc')));
        $this->_mockRequest->expects($this->any())->method('getParam')->will($this->returnValue('1'));

        $this->_mockSubscriptionService->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;

        // verify success message
        $this->_mockBackendModelSession->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been saved.'));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->saveAction();
    }

    /**
     * Test Save when action is not new, but there is no ID
     */
    public function testSaveActionNoId()
    {
        // Set the registry object to return 'new' so the 'create' path is followed
        $this->_mockRegistry->expects($this->any())->method('registry')->will($this->returnValue('old'));

        $this->_mockRequest->expects($this->any())->method('getPost')
            ->will($this->returnValue(array('apikey' => 'abc', 'name' => 'testSubscription')));

        // Use real translate model
        $this->_mockTranslateModel = null;

        // verify success message
        $this->_mockBackendModelSession->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'testSubscription\' has been saved.'));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->saveAction();
    }

    /**
     * Test deleteAction when subscription is an alias, not created by user.
     */
    public function testDeleteActionAlias()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                    array(
                         array('id', null, '1'),
                         array('apikey', null, '2'),
                         array('apisecret', null, 'secret'),
                         array('email', null, 'invalid.email.example.com'),
                         array('company', null, 'Example')
                    )
                ));

        $this->_mockSubscriptionService->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'),
                       'alias' => 'true'
                )
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;
        // Verify error message
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo('The subscription \'nameTest\' can not be removed.'));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->deleteAction();
    }

    public function testDeleteAction()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                    array(
                         array('id', null, '1'),
                         array('apikey', null, '2'),
                         array('apisecret', null, 'secret'),
                         array('email', null, 'invalid.email.example.com'),
                         array('company', null, 'Example')
                    )
                ));

        $this->_mockSubscriptionService->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;

        // verify success message
        $this->_mockBackendModelSession->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been removed.'));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->deleteAction();
    }

    public function testDeleteActionException ()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                    array(
                         array('id', null, '1'),
                         array('apikey', null, '2'),
                         array('apisecret', null, 'secret'),
                         array('email', null, 'invalid.email.example.com'),
                         array('company', null, 'Example')
                    )
                ));


        $this->_mockSubscriptionService->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // Have subscription service throw an exception to go down exception path
        $exceptionMessage = 'Exceptions happen.';
        $this->_mockSubscriptionService->expects($this->any())
            ->method('delete')
            ->will($this->throwException(new Mage_Core_Exception($exceptionMessage)));

        // Verify error
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo($exceptionMessage));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->deleteAction();
    }

    public function testRevokeAction()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                    array(
                         array('id', null, '1'),
                         array('apikey', null, '2'),
                         array('apisecret', null, 'secret'),
                         array('email', null, 'invalid.email.example.com'),
                         array('company', null, 'Example')
                    )
                ));

        $this->_mockSubscriptionService->expects($this->any())->method('revoke')->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;

        // verify success message
        $this->_mockBackendModelSession->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been revoked.'));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->revokeAction();
    }

    public function testRevokeActionNoData()
    {
        // Verify error
        $this->_mockTranslateModel = null;
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo('No Subscription ID was provided with the request.'));
        $this->_subscriptionController = $this->_createSubscriptionController();
        $this->_subscriptionController->revokeAction();
    }

    public function testRevokeActionException()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                    array(
                         array('id', null, '1'),
                         array('apikey', null, '2'),
                         array('apisecret', null, 'secret'),
                         array('email', null, 'invalid.email.example.com'),
                         array('company', null, 'Example')
                    )
                ));

        // Have subscription service throw an exception to go down exception path
        $exceptionMessage = 'Exceptions happen.';
        $this->_mockSubscriptionService->expects($this->any())
            ->method('revoke')
            ->will($this->throwException(new Mage_Core_Exception($exceptionMessage)));

        // Verify error
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo($exceptionMessage));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->revokeAction();
    }

    public function testActivateAction()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                    array(
                         array('id', null, '1'),
                         array('apikey', null, '2'),
                         array('apisecret', null, 'secret'),
                         array('email', null, 'invalid.email.example.com'),
                         array('company', null, 'Example')
                    )
                ));

        $this->_mockSubscriptionService->expects($this->once())->method('activate')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest')
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;

        // success message
        $this->_mockBackendModelSession->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been activated.'));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->activateAction();
    }

    public function testActivateActionNoData()
    {
        // Use real translate model
        $this->_mockTranslateModel = null;

        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo('No Subscription ID was provided with the request.'));
        $this->_subscriptionController = $this->_createSubscriptionController();
        $this->_subscriptionController->activateAction();
    }

    public function testActivateActionException()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                    array(
                         array('id', null, '1'),
                         array('apikey', null, '2'),
                         array('apisecret', null, 'secret'),
                         array('email', null, 'invalid.email.example.com'),
                         array('company', null, 'Example')
                    )
                ));

        // Have subscription service throw an exception to go down exception path
        $exceptionMessage = 'An exception occurred';
        $this->_mockSubscriptionService->expects($this->any())
            ->method('activate')
            ->will($this->throwException(new Mage_Core_Exception($exceptionMessage)));

        // Verify error
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo($exceptionMessage));

        $subscriptionController = $this->_createSubscriptionController();
        $subscriptionController->activateAction();
    }
}
