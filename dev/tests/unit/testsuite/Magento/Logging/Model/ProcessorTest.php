<?php

namespace Magento\Logging\Model;
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
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Logging\Model\Processor */
    protected $_model;

    /** @var  \Magento\Logging\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $_configMock;

    /** @var  \Magento\Logging\Model\Handler\Models|\PHPUnit_Framework_MockObject_MockObject */
    protected $_handlerModelsMock;

    /** @var  \Magento\Logging\Model\Handler\Controllers|\PHPUnit_Framework_MockObject_MockObject */
    protected $_controllersMock;

    /** @var  \Magento\Backend\Model\Auth\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $_authSessionMock;

    /** @var \Magento\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject  */
    protected $messageManager;

    /** @var  \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Logging\Model\\EventFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventFactoryMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $_requestMock;

    /** @var  \Magento\Logger|\PHPUnit_Framework_MockObject_MockObject */
    protected $_loggerMock;

    /** @var  \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject */
    protected $_quoteMock;

    /** @var  \Magento\Logging\Model\Event\Changes|\PHPUnit_Framework_MockObject_MockObject */
    protected $_changesMock;

    public function setUp()
    {
        $this->_configMock = $this->getMockBuilder('Magento\Logging\Model\Config')
            ->setMethods(array('getEventByFullActionName', 'isEventGroupLogged', 'getEventGroupConfig'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_handlerModelsMock = $this->getMockBuilder('Magento\Logging\Model\Handler\Models')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_controllersMock = $this->getMockBuilder('Magento\Logging\Model\Handler\Controllers')
            ->disableOriginalConstructor()
            ->getMock();

        $handlerFactoryMock = $this->getMock('Magento\Logging\Model\Handler\ControllersFactory', array('create'),
            array(), '', false);
        $handlerFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->_controllersMock));

        $this->_authSessionMock = $this->getMockBuilder('Magento\Backend\Model\Auth\Session')
            ->setMethods(array('getSkipLoggingAction', 'setSkipLoggingAction', 'isLoggedIn'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageManager = $this->getMockBuilder('Magento\Message\ManagerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->_objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')
            ->setMethods(array('create', 'get', 'configure', 'setFactory'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_eventFactoryMock = $this->getMock('Magento\Logging\Model\EventFactory', array('create'),
            array(), '', false);

        $this->_requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);

        $this->_loggerMock = $this->getMockBuilder('Magento\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject('Magento\Logging\Model\Processor', array(
            'config' => $this->_configMock,
            'modelsHandler' => $this->_handlerModelsMock,
            'authSession' => $this->_authSessionMock,
            'messageManager' => $this->messageManager,
            'objectManager' => $this->_objectManagerMock,
            'logger' => $this->_loggerMock,
            'handlerControllersFactory' => $handlerFactoryMock,
            'eventFactory' => $this->_eventFactoryMock,
            'request' => $this->_requestMock,
        ));
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

        $this->assertInstanceOf('Magento\Logging\Model\Processor', $this->_model->initAction($fullActionName, 'init'));
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
        $this->assertInstanceOf('Magento\Logging\Model\Processor', $this->_model->initAction($fullActionName, 'init'));
    }

    /**
     * @depends testInitActionSkipLogging
     */
    public function testModelActionAfterSkipNextAction($modelUnderTest)
    {
        $modelMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
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
        $loggingEventMock = $this->getMockBuilder('Magento\Logging\Model\Event')
            ->setMethods(array('setAction', 'setEventCode', 'setInfo', 'setIsSuccess', 'save', 'setData', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();

        $loggingEventMock->expects($this->any())
            ->method('setData');

        $this->_eventFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($loggingEventMock));

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

        $messages = new \Magento\Object(array('errors' => array()));
        $this->messageManager->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue($messages));

        $loggingEventMock = $this->getMockBuilder('Magento\Logging\Model\Event')
            ->setMethods(array('setAction', 'setEventCode', 'setInfo', 'setIsSuccess', 'save', '__wakeup'))
            ->disableOriginalConstructor()
            ->getMock();
        $loggingEventMock->expects($this->once())
            ->method('setAction')
            ->with($this->equalTo('init'));
        $loggingEventMock->expects($this->once())
            ->method('setEventCode')
            ->with($this->equalTo('test_events'));
        $loggingEventMock->expects($this->once())
            ->method('setInfo')
            ->with($this->equalTo('Access denied'));
        $loggingEventMock->expects($this->once())
            ->method('setIsSuccess')
            ->with($this->equalTo(0));
        $this->_eventFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($loggingEventMock));
        $this->_model->initAction($fullActionName, 'denied');

        $this->assertEquals($this->_model, $this->_model->logAction());
    }

    protected function _setUpModelActionAfter()
    {
        $eventGroupNode = array(
            'expected_models' => array('Magento\Sales\Model\Quote' => array())
        );

        $fullActionName = 'full_controller_action_name';
        $eventConfig = array(
            'action' => 'init',
            'group_name' => 'test_events',
            'skip_on_back' => array(
                'adminhtml_cms_page_version_edit'
            ),
            'expected_models' => array('Magento\Sales\Model\Quote' => array(
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

        /** @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject $modelMock */
        $this->_quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->setMethods(array('getId', 'getDataUsingMethod', '__wakeup'))
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

        $this->_changesMock = $this->getMockBuilder('Magento\Logging\Model\Event\Changes')
            ->setMethods(array('cleanupData', 'hasDifference', 'setSourceName', 'setSourceId', 'save', '__wakeup'))
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
            ->with($this->equalTo('Magento\Sales\Model\Quote'));

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

        $messages = new \Magento\Object(array('errors' => array()));
        $this->messageManager->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue($messages));

        $loggingMock = $this->getMockBuilder('Magento\Logging\Model\Event')
            ->setMethods(array('getId', 'setAction', 'setEventCode', 'setInfo', 'setIsSuccess', 'save',
                    'setAdditionalInfo', '__wakeup'))
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
        $this->_eventFactoryMock->expects($this->any())->method('create')
        ->will($this->returnValue($loggingMock));

        $this->_controllersMock->expects($this->once())
            ->method('postDispatchGeneric')
            ->will($this->returnValue(true));

        $this->_model->initAction('full_controller_action_name', 'init');
        $this->_model->modelActionAfter($this->_quoteMock, 'save');
        $this->_model->logAction();
    }
}
