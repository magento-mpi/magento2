<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App\Action;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ActionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\App\Action\ActionFake */
    protected $action;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $contextMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var \Magento\Framework\App\ActionFlag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_actionFlagMock;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_redirectMock;

    /**
     * @var \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewMock;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    const FULL_ACTION_NAME = 'module/controller/someaction';

    const ROUTE_NAME = 'module/controller/actionroute';

    const ACTION_NAME = 'someaction';

    const CONTROLLER_NAME = 'controller';

    const MODULE_NAME = 'module';

    public static $actionParams = ['param' => 'value'];

    protected function setUp()
    {
        $this->_eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $this->_urlMock = $this->getMock('Magento\Framework\UrlInterface', [], [], '', false);
        $this->_actionFlagMock = $this->getMock('Magento\Framework\App\ActionFlag', [], [], '', false);
        $this->_redirectMock = $this->getMock('Magento\Framework\App\Response\RedirectInterface', [], [], '', false);
        $this->_viewMock = $this->getMock('Magento\Framework\App\ViewInterface', [], [], '', false);
        $this->_messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface', [], [], '', false);
        $this->_objectManagerMock = $this->getMock('Magento\Framework\ObjectManager', [], [], '', false);
        $this->_requestMock = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            [
                'getFullActionName',
                'getRouteName',
                'isDispatched',
                'initForward',
                'setParams',
                'setControllerName',
                'setDispatched',
                'getModuleName',
                'setModuleName',
                'getActionName',
                'setActionName',
                'getParam'
            ],
            [],
            '',
            false
        );
        $this->_responseMock = $this->getMock('Magento\Framework\App\ResponseInterface', [], [], '', false);

        $this->contextMock = $this->getMock('Magento\Framework\App\Action\Context', [], [], '', false);
        $this->contextMock->expects($this->once())->method('getObjectManager')->will(
            $this->returnValue($this->_objectManagerMock)
        );
        $this->contextMock->expects($this->once())->method('getEventManager')->will(
            $this->returnValue($this->_eventManagerMock)
        );
        $this->contextMock->expects($this->once())->method('getUrl')->will(
            $this->returnValue($this->_urlMock)
        );
        $this->contextMock->expects($this->once())->method('getActionFlag')->will(
            $this->returnValue($this->_actionFlagMock)
        );
        $this->contextMock->expects($this->once())->method('getRedirect')->will(
            $this->returnValue($this->_redirectMock)
        );
        $this->contextMock->expects($this->once())->method('getView')->will(
            $this->returnValue($this->_viewMock)
        );
        $this->contextMock->expects($this->once())->method('getMessageManager')->will(
            $this->returnValue($this->_messageManagerMock)
        );
        $this->contextMock->expects($this->once())->method('getRequest')->will(
            $this->returnValue($this->_requestMock)
        );
        $this->contextMock->expects($this->once())->method('getResponse')->will(
            $this->returnValue($this->_responseMock)
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->action = $this->objectManagerHelper->getObject(
            'Magento\Framework\App\Action\ActionFake',
            [
                'context' => $this->contextMock
            ]
        );
        \Magento\Framework\Profiler::disable();
    }

    public function testDispatchPostDispatch()
    {
        $this->_requestMock->expects($this->exactly(3))->method('getFullActionName')->will(
            $this->returnValue(self::FULL_ACTION_NAME)
        );
        $this->_requestMock->expects($this->exactly(2))->method('getRouteName')->will(
            $this->returnValue(self::ROUTE_NAME)
        );
        $expectedEventParameters = ['controller_action' => $this->action, 'request' => $this->_requestMock];
        $this->_eventManagerMock->expects($this->at(0))->method('dispatch')->with(
            'controller_action_predispatch',
            $expectedEventParameters
        );
        $this->_eventManagerMock->expects($this->at(1))->method('dispatch')->with(
            'controller_action_predispatch_' . self::ROUTE_NAME,
            $expectedEventParameters
        );
        $this->_eventManagerMock->expects($this->at(2))->method('dispatch')->with(
            'controller_action_predispatch_' . self::FULL_ACTION_NAME,
            $expectedEventParameters
        );

        $this->_requestMock->expects($this->once())->method('isDispatched')->will($this->returnValue(true));
        $this->_actionFlagMock->expects($this->at(0))->method('get')->with('', Action::FLAG_NO_DISPATCH)->will(
            $this->returnValue(false)
        );

        $this->_requestMock->expects($this->once())->method('getActionName')->will(
            $this->returnValue(self::ACTION_NAME)
        );

        // _forward expectations
        $this->_requestMock->expects($this->once())->method('initForward');
        $this->_requestMock->expects($this->once())->method('setParams')->with(self::$actionParams);
        $this->_requestMock->expects($this->once())->method('setControllerName')->with(self::CONTROLLER_NAME);
        $this->_requestMock->expects($this->once())->method('setModuleName')->with(self::MODULE_NAME);
        $this->_requestMock->expects($this->once())->method('setActionName')->with(self::ACTION_NAME);
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(false);

        // _redirect expectations
        $this->_redirectMock->expects($this->once())->method('redirect')->with(
            $this->_responseMock,
            self::FULL_ACTION_NAME,
            self::$actionParams
        );

        $this->_actionFlagMock->expects($this->at(1))->method('get')->with('', Action::FLAG_NO_POST_DISPATCH)->will(
            $this->returnValue(false)
        );

        $this->_eventManagerMock->expects($this->at(3))->method('dispatch')->with(
            'controller_action_postdispatch_' . self::FULL_ACTION_NAME,
            $expectedEventParameters
        );
        $this->_eventManagerMock->expects($this->at(4))->method('dispatch')->with(
            'controller_action_postdispatch_' . self::ROUTE_NAME,
            $expectedEventParameters
        );
        $this->_eventManagerMock->expects($this->at(5))->method('dispatch')->with(
            'controller_action_postdispatch',
            $expectedEventParameters
        );

        $this->assertEquals($this->_responseMock, $this->action->dispatch($this->_requestMock));
    }
}

class ActionFake extends Action
{
    /**
     * Fake action to check a method call from a parent
     */
    public function someactionAction()
    {
        $this->_forward(
            ActionTest::ACTION_NAME,
            ActionTest::CONTROLLER_NAME,
            ActionTest::MODULE_NAME,
            ActionTest::$actionParams);
        $this->_redirect(ActionTest::FULL_ACTION_NAME, ActionTest::$actionParams);
        return;
    }
}
