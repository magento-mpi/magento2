<?php
/**
 * Magento_Webhook_Controller_Adminhtml_Webhook_Subscription
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Controller_Adminhtml_Webhook_SubscriptionTest extends PHPUnit_Framework_TestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockObjectManager;

    /** @var Magento_Webhook_Controller_Adminhtml_Webhook_Subscription */
    protected $_subscriptionContr;

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
    protected $_mockBackendModSess;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockBackendCntCtxt;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockSubscriptionSvc;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockRegistry;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockRequest;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockConfigScope;

    /**
     * Setup object manager and initialize mocks
     */
    public function setUp()
    {
        /** @var Magento_Test_Helper_ObjectManager $objectManagerHelper */
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_setMageObjectManager();

        // Initialize mocks which are used in several test cases
        $this->_mockApp = $this->getMockBuilder('Magento_Core_Model_App')
            ->setMethods( array('getConfig'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockConfig = $this->getMockBuilder('Magento_Core_Model_Config')->disableOriginalConstructor()
            ->getMock();
        $this->_mockApp->expects($this->any())->method('getConfig')->will($this->returnValue($this->_mockConfig));
        $this->_mockEventManager = $this->getMockBuilder('Magento_Core_Model_Event_Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockLayoutFilter = $this->getMockBuilder('Magento_Core_Model_Layout_Filter_Acl')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockBackendModSess = $this->getMockBuilder('Magento_Backend_Model_Session')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockTranslateModel = $this->getMockBuilder('Magento_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockSubscriptionSvc = $this->getMockBuilder('Magento_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockRequest = $this->getMockBuilder('Magento_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockRegistry = $this->getMockBuilder('Magento_Core_Model_Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockConfigScope = $this->getMockBuilder('Magento_Config_ScopeInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Reset object manager.
     */
    public function tearDown()
    {
        Mage::reset();
    }

    public function testIndexAction()
    {
        $this->_verifyLoadAndRenderLayout();

        // renderLayout
        $this->_subscriptionContr = $this->_createSubscriptionController();
        $this->_subscriptionContr->indexAction();
    }

    public function testNewAction()
    {
        // verify the request is forwarded to 'edit' action
        $this->_mockRequest->expects($this->any())->method('setActionName')->with('edit')
            ->will( $this->returnValue($this->_mockRequest));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->newAction();
    }

    public function testEditActionHasData()
    {
        // put data in session, the magic function getFormData is called so, must match __call method name
        $this->_mockBackendModSess->expects($this->any())
            ->method('__call')->will($this->returnValue(array('testkey' =>'testvalue')));

        $this->_verifyLoadAndRenderLayout();

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->editAction();
    }

    public function testEditActionNoDataAdd()
    {
        // Set the registry object to return 'new' so the 'Add Subscription' path is followed
        $this->_mockRegistry->expects($this->any())->method('registry')->will($this->returnValue('new'));

        $this->_verifyLoadAndRenderLayout();

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->editAction();
    }

    public function testEditException()
    {
        $exceptionMessage = 'An exception happened';
        // have load layout throw an exception
        $this->_mockObjectManager->expects($this->at(0))
            ->method('get')
            ->with('Magento_Config_ScopeInterface')
            ->will($this->throwException(new Magento_Core_Exception($exceptionMessage)));

        // verify the error
        $this->_mockBackendModSess->expects($this->once())
            ->method('addError')
            ->with($this->equalTo($exceptionMessage));

        $this->_subscriptionContr = $this->_createSubscriptionController();
        $this->_subscriptionContr->editAction();
    }

    public function testSaveAction()
    {
        // Use real translate model
        $this->_mockTranslateModel = null;

        $this->_mockRequest->expects($this->any())
            ->method('getPost')->will($this->returnValue(array('apikey' => 'abc')));
        $this->_mockRequest->expects($this->any())->method('getParam')->will($this->returnValue('1'));

        $this->_mockSubscriptionSvc->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // verify success message
        $this->_mockBackendModSess->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been saved.'));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->saveAction();
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
        $this->_mockBackendModSess->expects($this->once())->method('addError')
            ->with($this->equalTo('The subscription \'\' has not been saved, as no data was provided.'));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->saveAction();
    }

    public function testSaveActionException()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')->will($this->returnValue('1'));

        // Have subscription service throw an exception to test exception path
        $exceptionMessage = 'an exception happened';
        $this->_mockSubscriptionSvc->expects($this->any())
            ->method('get')
            ->with(1)
            ->will($this->throwException(new Magento_Core_Exception($exceptionMessage)));

        // Verify error
        $this->_mockBackendModSess->expects($this->once())->method('addError')
            ->with($this->equalTo($exceptionMessage));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->saveAction();
    }

    public function testSaveActionNew()
    {
        // Set the registry object to return 'new' so the 'create' path is followed
        $this->_mockRegistry->expects($this->any())->method('registry')->will($this->returnValue('new'));

        $this->_mockRequest->expects($this->any())->method('getPost')
            ->will($this->returnValue(array('apikey' => 'abc')));
        $this->_mockRequest->expects($this->any())->method('getParam')->will($this->returnValue('1'));

        $this->_mockSubscriptionSvc->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;

        // verify success message
        $this->_mockBackendModSess->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been saved.'));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->saveAction();
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
        $this->_mockBackendModSess->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'testSubscription\' has been saved.'));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->saveAction();
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

        $this->_mockSubscriptionSvc->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'),
                       'alias' => 'true'
                )
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;
        // Verify error message
        $this->_mockBackendModSess->expects($this->once())->method('addError')
            ->with($this->equalTo('The subscription \'nameTest\' can not be removed.'));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->deleteAction();
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

        $this->_mockSubscriptionSvc->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;

        // verify success message
        $this->_mockBackendModSess->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been removed.'));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->deleteAction();
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


        $this->_mockSubscriptionSvc->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // Have subscription service throw an exception to go down exception path
        $exceptionMessage = 'Exceptions happen.';
        $this->_mockSubscriptionSvc->expects($this->any())
            ->method('delete')
            ->will($this->throwException(new Magento_Core_Exception($exceptionMessage)));

        // Verify error
        $this->_mockBackendModSess->expects($this->once())->method('addError')
            ->with($this->equalTo($exceptionMessage));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->deleteAction();
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

        $this->_mockSubscriptionSvc->expects($this->any())->method('revoke')->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;

        // verify success message
        $this->_mockBackendModSess->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been revoked.'));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->revokeAction();
    }

    public function testRevokeActionNoData()
    {
        // Verify error
        $this->_mockTranslateModel = null;
        $this->_mockBackendModSess->expects($this->once())->method('addError')
            ->with($this->equalTo('No Subscription ID was provided with the request.'));
        $this->_subscriptionContr = $this->_createSubscriptionController();
        $this->_subscriptionContr->revokeAction();
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
        $this->_mockSubscriptionSvc->expects($this->any())
            ->method('revoke')
            ->will($this->throwException(new Magento_Core_Exception($exceptionMessage)));

        // Verify error
        $this->_mockBackendModSess->expects($this->once())->method('addError')
            ->with($this->equalTo($exceptionMessage));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->revokeAction();
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

        $this->_mockSubscriptionSvc->expects($this->once())->method('activate')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest')
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;

        // success message
        $this->_mockBackendModSess->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been activated.'));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->activateAction();
    }

    public function testActivateActionNoData()
    {
        // Use real translate model
        $this->_mockTranslateModel = null;

        $this->_mockBackendModSess->expects($this->once())->method('addError')
            ->with($this->equalTo('No Subscription ID was provided with the request.'));
        $this->_subscriptionContr = $this->_createSubscriptionController();
        $this->_subscriptionContr->activateAction();
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
        $this->_mockSubscriptionSvc->expects($this->any())
            ->method('activate')
            ->will($this->throwException(new Magento_Core_Exception($exceptionMessage)));

        // Verify error
        $this->_mockBackendModSess->expects($this->once())->method('addError')
            ->with($this->equalTo($exceptionMessage));

        $subscriptionContr = $this->_createSubscriptionController();
        $subscriptionContr->activateAction();
    }

    /**
     * Makes sure that Mage has a mock object manager set.
     *
     */
    protected function _setMageObjectManager()
    {
        Mage::reset();
        $this->_mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        Magento_Test_Helper_Bootstrap::setObjectManager($this->_mockObjectManager);
    }

    /**
     * Creates the SubscriptionController to test.
     *
     * @return Magento_Webhook_Controller_Adminhtml_Webhook_Subscription
     */
    protected function _createSubscriptionController()
    {
        // Mock Layout passed into constructor
        $layoutMock = $this->getMockBuilder('Magento_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMergeMock = $this->getMockBuilder('Magento_Core_Model_Layout_Merge')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->any())->method('getUpdate')->will($this->returnValue($layoutMergeMock));
        $testElement = new Magento_Simplexml_Element('<test>test</test>');
        $layoutMock->expects($this->any())->method('getNode')->will($this->returnValue($testElement));

        // for _setActiveMenu
        $blockMock = $this->getMockBuilder('Magento_Backend_Block_Menu')
            ->disableOriginalConstructor()
            ->getMock();
        $menuMock = $this->getMockBuilder('Magento_Backend_Model_Menu')
            ->disableOriginalConstructor()
            ->getMock();
        $menuMock->expects($this->any())->method('getParentItems')->will($this->returnValue(array()));
        $blockMock->expects($this->any())->method('getMenuModel')->will($this->returnValue($menuMock));

        $layoutMock->expects($this->any())->method('getMessagesBlock')->will($this->returnValue($blockMock));
        $layoutMock->expects($this->any())->method('getBlock')->will($this->returnValue($blockMock));

        $contextParameters = array(
            'layout' => $layoutMock,
            'objectManager' => $this->_mockObjectManager,
            'session' => $this->_mockBackendModSess,
            'translator' => $this->_mockTranslateModel,
            'request' => $this->_mockRequest,
        );

        $this->_mockBackendCntCtxt = $this->_objectManagerHelper
            ->getObject('Magento_Backend_Controller_Context',
                $contextParameters);

        $subControllerParams = array(
            'context' => $this->_mockBackendCntCtxt,
            'subscriptionService' => $this->_mockSubscriptionSvc,
            'registry' => $this->_mockRegistry,
        );

        /** Create SubscriptionController to test */
        $subscriptionContr = $this->_objectManagerHelper
            ->getObject('Magento_Webhook_Controller_Adminhtml_Webhook_Subscription',
                $subControllerParams);
        return $subscriptionContr;
    }

    /**
     * Common mock 'expect' pattern.
     * Calls that need to be mocked out when
     * Magento_Backend_Controller_ActionAbstract loadLayout() and renderLayout() are called.
     */
    protected function _verifyLoadAndRenderLayout()
    {
        $map = array(
            array('Magento_Core_Model_Config', $this->_mockConfig),
            array('Magento_Core_Model_Layout_Filter_Acl', $this->_mockLayoutFilter),
            array('Magento_Backend_Model_Session', $this->_mockBackendModSess),
            array('Magento_Core_Model_Translate', $this->_mockTranslateModel),
            array('Magento_Config_ScopeInterface', $this->_mockConfigScope),
        );
        $this->_mockObjectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));
    }
}
