<?php
/**
 * Test
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Logging_Model_ProcessorTest extends PHPUnit_Framework_TestCase
{
    /** @var  Magento_Logging_Model_Processor */
    protected $_model;

    /** @var  Magento_Logging_Model_Config|PHPUnit_Framework_MockObject_MockObject */
    protected $_configMock;

    /** @var  Magento_Logging_Model_Handler_Models|PHPUnit_Framework_MockObject_MockObject */
    protected $_handlerModelsMock;

    /** @var  Magento_Logging_Model_Handler_Controllers|PHPUnit_Framework_MockObject_MockObject */
    protected $_controllersMock;

    /** @var  Magento_Backend_Model_Auth_Session|PHPUnit_Framework_MockObject_MockObject */
    protected $_authSessionMock;

    /** @var Magento_Backend_Model_Session|PHPUnit_Framework_MockObject_MockObject  */
    protected $_backendSessionMock;

    /** @var  Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    /** @var  Magento_Core_Model_App|PHPUnit_Framework_MockObject_MockObject */
    protected $_coreAppMock;

    /** @var  Magento_Core_Helper_Http|PHPUnit_Framework_MockObject_MockObject */
    protected $_httpHelperMock;

    /** @var  Magento_Core_Model_Logger|PHPUnit_Framework_MockObject_MockObject */
    protected $_loggerMock;

    /** @var  Magento_Sales_Model_Quote|PHPUnit_Framework_MockObject_MockObject */
    protected $_quoteMock;

    /** @var  Magento_Logging_Model_Event_Changes|PHPUnit_Framework_MockObject_MockObject */
    protected $_changesMock;

    public function setUp()
    {

        $this->_configMock = $this->getMockBuilder('Magento_Logging_Model_Config')
            ->setMethods(array('getEventByFullActionName', 'isEventGroupLogged', 'getEventGroupConfig'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_handlerModelsMock = $this->getMockBuilder('Magento_Logging_Model_Handler_Models')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_controllersMock = $this->getMockBuilder('Magento_Logging_Model_Handler_Controllers')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_authSessionMock = $this->getMockBuilder('Magento_Backend_Model_Auth_Session')
            ->setMethods(array('getSkipLoggingAction', 'setSkipLoggingAction', 'isLoggedIn'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_backendSessionMock = $this->getMockBuilder('Magento_Backend_Model_Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')
            ->setMethods(array('create', 'get', 'configure'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_coreAppMock = $this->getMockBuilder('Magento_Core_Model_App')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_httpHelperMock = $this->getMockBuilder('Magento_Core_Helper_Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_loggerMock = $this->getMockBuilder('Magento_Core_Model_Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_model = new Magento_Logging_Model_Processor(
            $this->_configMock,
            $this->_handlerModelsMock,
            $this->_controllersMock,
            $this->_authSessionMock,
            $this->_backendSessionMock,
            $this->_objectManagerMock,
            $this->_coreAppMock,
            $this->_httpHelperMock,
            $this->_loggerMock
        );
    }

    public function testInitActionSkipLogging()
    {
        $fullActionName = 'full_controller_action_name';
        $eventConfig = array(
            'action' => 'init',
            'group_name' => 'test_events'
        );
        $this->_configMock->expects($this->once())
            ->method('getEventByFullActionName')
            ->with($this->equalTo($fullActionName))
            ->will($this->returnValue($eventConfig));

        $this->_configMock->expects($this->once())
            ->method('isEventGroupLogged')
            ->with($this->equalTo('test_events'))
            ->will($this->returnValue(true));

        $sessionValue = array(
            $fullActionName,
            'full_controller_action_othername'
        );
        $this->_authSessionMock->expects($this->once())
            ->method('getSkipLoggingAction')
            ->will($this->returnValue($sessionValue));

        $this->_authSessionMock->expects($this->once())
            ->method('setSkipLoggingAction')
            ->with($this->equalTo(array('1' => 'full_controller_action_othername')))
            ->will($this->returnValue(true));

        $this->assertInstanceOf('Magento_Logging_Model_Processor', $this->_model->initAction($fullActionName, 'init'));
        return $this->_model;
    }

    public function testInitActionSkipOnBack()
    {
        $fullActionName = 'full_controller_action_name';
        $eventConfig = array(
            'action' => 'init',
            'group_name' => 'test_events',
            'skip_on_back' => array(
                'adminhtml_cms_page_version_edit'
            ),
        );
        $this->_configMock->expects($this->once())
            ->method('getEventByFullActionName')
            ->with($this->equalTo($fullActionName))
            ->will($this->returnValue($eventConfig));

        $this->_configMock->expects($this->once())
            ->method('isEventGroupLogged')
            ->with($this->equalTo('test_events'))
            ->will($this->returnValue(true));

        $skippedLoggingAction = 'full_controller_action_othername,full_controller_action_thirdname';

        $skippedAfter = array(
            'adminhtml_cms_page_version_edit',
            'full_controller_action_othername',
            'full_controller_action_thirdname',
        );
        $this->_authSessionMock->expects($this->once())
            ->method('getSkipLoggingAction')
            ->will($this->returnValue($skippedLoggingAction));
        $this->_authSessionMock->expects($this->once())
            ->method('setSkipLoggingAction')
            ->with($this->equalTo($skippedAfter))
            ->will($this->returnValue(true));
        $this->assertInstanceOf('Magento_Logging_Model_Processor', $this->_model->initAction($fullActionName, 'init'));
    }

    /**
     * @depends testInitActionSkipLogging
     */
    public function testModelActionAfterSkipNextAction($modelUnderTest)
    {
        $modelMock = $this->getMockBuilder('Magento_Sales_Model_Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertFalse($modelUnderTest->modelActionAfter($modelMock, 'save'));
    }

    public function testModelActionAfter()
    {
        $this->_setUpModelActionAfter();
        $this->_model->initAction('full_controller_action_name', 'init');
        $this->assertEquals($this->_model, $this->_model->modelActionAfter($this->_quoteMock, 'save'));
    }


    public function testLogActionNotInited()
    {
        $this->assertFalse($this->_model->logAction());
    }

    public function testLogActionDenied()
    {
        $fullActionName = 'full_controller_action_name';
        $eventConfig = array(
            'action' => 'init',
            'group_name' => 'test_events',
        );
        $this->_configMock->expects($this->once())
            ->method('getEventByFullActionName')
            ->with($this->equalTo($fullActionName))
            ->will($this->returnValue($eventConfig));

        $this->_configMock->expects($this->exactly(2))
            ->method('isEventGroupLogged')
            ->with($this->equalTo('test_events'))
            ->will($this->returnValue(true));

        $this->_authSessionMock->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $messages = new Magento_Object(array('errors' => array()));
        $this->_backendSessionMock->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue($messages));

        $request = new Magento_Object(array('server' => array('HTTP_X_FORWARDED_FOR')));
        $this->_coreAppMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $loggingMock = $this->getMockBuilder('Magento_Logging_Model_Event')
            ->setMethods(array('setAction', 'setEventCode', 'setInfo', 'setIsSuccess', 'save'))
            ->disableOriginalConstructor()
            ->getMock();

        $loggingMock->expects($this->once())
            ->method('setAction')
            ->with($this->equalTo('init'));
        $loggingMock->expects($this->once())
            ->method('setEventCode')
            ->with($this->equalTo('test_events'));
        $loggingMock->expects($this->once())
            ->method('setInfo')
            ->with($this->equalTo('Access denied'));
        $loggingMock->expects($this->once())
            ->method('setIsSuccess')
            ->with($this->equalTo(0));

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Logging_Model_Event'))
            ->will($this->returnValue($loggingMock));

        $this->_model->initAction($fullActionName, 'denied');
        $this->assertEquals($this->_model, $this->_model->logAction());
    }

    protected function _setUpModelActionAfter()
    {
        $eventGroupNode = array(
            'expected_models' => array('Magento_Sales_Model_Quote' => array())
        );

        $fullActionName = 'full_controller_action_name';
        $eventConfig = array(
            'action' => 'init',
            'group_name' => 'test_events',
            'skip_on_back' => array(
                'adminhtml_cms_page_version_edit'
            ),
            'expected_models' => array('Magento_Sales_Model_Quote' => array(
                'additional_data' => array('item_id', 'quote_id', 'new_password'),
                'skip_data' => array('new_password', 'password', 'password_hash')
            ))
        );
        $this->_configMock->expects($this->once())
            ->method('getEventByFullActionName')
            ->with($this->equalTo($fullActionName))
            ->will($this->returnValue($eventConfig));

        $this->_configMock->expects($this->once())
            ->method('isEventGroupLogged')
            ->with($this->equalTo('test_events'))
            ->will($this->returnValue(true));

        $this->_configMock->expects($this->once())
            ->method('getEventGroupConfig')
            ->with($this->equalTo('test_events'))
            ->will($this->returnValue($eventGroupNode));

        /** @var Magento_Sales_Model_Quote|PHPUnit_Framework_MockObject_MockObject $modelMock */
        $this->_quoteMock = $this->getMockBuilder('Magento_Sales_Model_Quote')
            ->setMethods(array('getId', 'getDataUsingMethod'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_quoteMock->expects($this->at(0))
            ->method('getId')
            ->will($this->returnValue(1));

        $this->_quoteMock->expects($this->at(1))
            ->method('getDataUsingMethod')
            ->with($this->equalTo('item_id'))
            ->will($this->returnValue(2));

        $this->_quoteMock->expects($this->at(2))
            ->method('getDataUsingMethod')
            ->with($this->equalTo('quote_id'))
            ->will($this->returnValue(3));

        $this->_quoteMock->expects($this->at(3))
            ->method('getId')
            ->will($this->returnValue(1));

        $this->_changesMock = $this->getMockBuilder('Magento_Logging_Model_Event_Changes')
            ->setMethods(array('cleanupData', 'hasDifference', 'setSourceName', 'setSourceId', 'save'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_changesMock->expects($this->once())
            ->method('cleanupData')
            ->with($this->equalTo(array('new_password', 'password', 'password_hash')));

        $this->_changesMock->expects($this->once())
            ->method('hasDifference')
            ->will($this->returnValue(true));

        $this->_changesMock->expects($this->once())
            ->method('setSourceName')
            ->with($this->equalTo('Magento_Sales_Model_Quote'));

        $this->_changesMock->expects($this->once())
            ->method('setSourceId')
            ->with($this->equalTo(1));

        $this->_handlerModelsMock->expects($this->once())
            ->method('modelSaveAfter')
            ->with($this->equalTo($this->_quoteMock), $this->equalTo($this->_model))
            ->will($this->returnValue($this->_changesMock));
    }

    public function testLogAction()
    {
        $this->_setUpModelActionAfter();

        $messages = new Magento_Object(array('errors' => array()));
        $this->_backendSessionMock->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue($messages));

        $request = new Magento_Object(array('server' => array('HTTP_X_FORWARDED_FOR')));
        $this->_coreAppMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $loggingMock = $this->getMockBuilder('Magento_Logging_Model_Event')
            ->setMethods(array('getId', 'setAction', 'setEventCode', 'setInfo', 'setIsSuccess', 'save',
                    'setAdditionalInfo'))
            ->disableOriginalConstructor()
            ->getMock();
        $loggingMock->expects($this->once())
            ->method('setAction')
            ->with($this->equalTo('init'));
        $loggingMock->expects($this->once())
            ->method('setEventCode')
            ->with($this->equalTo('test_events'));

        $additionalInfo = array(1 => array('item_id' => 2, 'quote_id' => 3));
        $loggingMock->expects($this->once())
            ->method('setAdditionalInfo')
            ->with($this->contains($additionalInfo));
        $loggingMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Logging_Model_Event'))
            ->will($this->returnValue($loggingMock));
        $this->_controllersMock->expects($this->once())
            ->method('postDispatchGeneric')
            ->will($this->returnValue(true));

        $this->_model->initAction('full_controller_action_name', 'init');
        $this->_model->modelActionAfter($this->_quoteMock, 'save');
        $this->_model->logAction();
    }
}