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

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockLayoutFilter;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockBackendModelSession;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockBackendControllerContext;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockRequest;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockSubscriptionService;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockResponse;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockBackendHelperData;
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
     * @return object
     */
    protected function _createRegistrationController()
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
        $blockMock = $this->getMockBuilder('Mage_Core_Block_Abstract')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->any())->method('getMessagesBlock')->will($this->returnValue($blockMock));

        $contextParameters = array(
            'layout' => $layoutMock,
            'objectManager' => $this->_mockObjectManager,
            'session' => $this->_mockBackendModelSession,
            'request' => $this->_mockRequest,
            'response' => $this->_mockResponse,
            'helper' => $this->_mockBackendHelperData,
            'translator' => $this->_mockTranslateModel,
        );

        $this->_mockBackendControllerContext = $this->_objectManagerHelper
            ->getObject('Mage_Backend_Controller_Context',
                $contextParameters);

        $regControllerParams = array(
            'context' => $this->_mockBackendControllerContext,
            'subscriptionService' => $this->_mockSubscriptionService,
        );

        /** Create SubscriptionController to test */
        $registrationController = $this->_objectManagerHelper
            ->getObject('Mage_Webhook_Adminhtml_Webhook_RegistrationController',
                $regControllerParams);
        return $registrationController;
    }

    public function setUp()
    {
        /** @var Magento_Test_Helper_ObjectManager $objectManagerHelper */
        $this->_objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_setMageObjectManager();

        $this->_mockBackendHelperData = $this->getMockBuilder('Mage_Backend_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();

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
        $this->_mockBackendModelSession = $this->getMockBuilder('Mage_Backend_Model_Session')->getMock();
        $this->_mockTranslateModel = $this->getMockBuilder('Mage_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockSubscriptionService = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockRequest = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->getMock();
        $this->_mockResponse = $this->getMockBuilder('Mage_Core_Controller_Response_Http')
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

    public function testActivateActionException()
    {
        $expectedMessage = 'not subscribed';
        $this->_mockSubscriptionService->expects($this->any())->method('get')
            ->will($this->throwException(new Mage_Core_Exception($expectedMessage)));

        // verify the error
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo($expectedMessage));
        $registrationController = $this->_createRegistrationController();
        $registrationController->activateAction();
    }

    public function testActivateAction()
    {
        $this->_verifyLoadAndRenderLayout();
        $this->_registrationController = $this->_createRegistrationController();
        $this->_registrationController->activateAction();
    }

    public function testAcceptAction()
    {
        // Verify redirect to registration
        $this->_mockBackendHelperData->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('*/webhook_registration/user'), array('id' => ''));
        $this->_registrationController = $this->_createRegistrationController();
        $this->_registrationController->acceptAction();
    }

    public function testAcceptActionNotSubscribed()
    {
        $expectedMessage = 'not subscribed';
        $this->_mockSubscriptionService->expects($this->any())->method('get')
            ->will($this->throwException(new Mage_Core_Exception($expectedMessage)));

        // verify the error
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo($expectedMessage));

        $registrationController = $this->_createRegistrationController();
        $registrationController->acceptAction();
    }

    public function testUserAction()
    {
        $this->_verifyLoadAndRenderLayout();
        $this->_registrationController = $this->_createRegistrationController();
        $this->_registrationController->userAction();
    }

    public function testUserActionNotSubscribed()
    {
        $expectedMessage = 'not subscribed';
        $this->_mockSubscriptionService->expects($this->any())->method('get')
            ->will($this->throwException(new Mage_Core_Exception($expectedMessage)));

        // verify the error
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo($expectedMessage));

        $registrationController = $this->_createRegistrationController();
        $registrationController->userAction();
    }

    public function testRegisterActionNoData()
    {
        // Use real translate model
        $this->_mockTranslateModel = null;

        // Verify error message
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo('API Key, API Secret and Contact Email are required fields.'));
        $registrationController = $this->_createRegistrationController();
        $registrationController->registerAction();
    }

    public function testRegisterActionInvalidEmail()
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

        // Use real translate model
        $this->_mockTranslateModel = null;

        // Verify error message
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo('Invalid Email address provided'));
        $registrationController = $this->_createRegistrationController();
        $registrationController->registerAction();
    }

    public function testRegisterAction()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                array(
                    array('id', null, '1'),
                    array('apikey', null, '2'),
                    array('apisecret', null, 'secret'),
                    array('email', null, 'test@example.com'),
                    array('company', null, 'Example')
                )
            ));

        $this->_mockSubscriptionService->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest',
                       'subscription_id' => '1',
                       'topics' => array('topic1', 'topic2'))
            ));
        $this->_mockSubscriptionService->expects($this->any())->method('update')->will($this->returnArgument(0));

        $registrationController = $this->_createRegistrationController();

        // Verify redirect to success page
        $this->_mockBackendHelperData->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('*/webhook_registration/succeeded'), array('id' => '1'));

        $registrationController->registerAction();
    }

    public function testFailedAction()
    {
        $this->_verifyLoadAndRenderLayout();
        $this->_registrationController = $this->_createRegistrationController();
        $this->_registrationController->failedAction();
    }

    public function testSucceededAction()
    {
        $this->_mockRequest->expects($this->any())->method('getParam')
            ->will( $this->returnValueMap(
                    array(
                         array('id', null, '1'),
                    )
                ));

        $this->_verifyLoadAndRenderLayout();

        $this->_mockSubscriptionService->expects($this->any())->method('get')->with(1)->will($this->returnValue(
                array( 'name' => 'nameTest' )
            ));

        // Use real translate model
        $this->_mockTranslateModel = null;

        // verify success message
        $this->_mockBackendModelSession->expects($this->once())->method('addSuccess')
            ->with($this->equalTo('The subscription \'nameTest\' has been activated.'));

        $this->_registrationController = $this->_createRegistrationController();
        $this->_registrationController->succeededAction();
    }

    public function testSucceededActionNotSubscribed()
    {
        $this->_verifyLoadAndRenderLayout();
        $this->_mockSubscriptionService = $this->getMockBuilder('Mage_Webhook_Service_SubscriptionV1')
            ->disableOriginalConstructor()
            ->getMock();
        $expectedMessage = 'not subscribed';
        $this->_mockSubscriptionService->expects($this->any())->method('get')
            ->will($this->throwException(new Mage_Core_Exception($expectedMessage)));
        // verify the error
        $this->_mockBackendModelSession->expects($this->once())->method('addError')
            ->with($this->equalTo($expectedMessage));

        $registrationController = $this->_createRegistrationController();
        $registrationController->succeededAction();
    }
}
