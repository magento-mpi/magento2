<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mview;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Mview\View
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Mview\ConfigInterface
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Mview\ActionFactory
     */
    protected $actionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Mview\View\State
     */
    protected $stateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Mview\View\Changelog
     */
    protected $changelogMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Mview\View\SubscriptionFactory
     */
    protected $subscriptionFactoryMock;

    protected function setUp()
    {
        $this->configMock = $this->getMockForAbstractClass(
            'Magento\Mview\ConfigInterface', array(), '', false, false, true, array('getView')
        );
        $this->actionFactoryMock = $this->getMock(
            'Magento\Mview\ActionFactory', array('get'), array(), '', false
        );
        $this->stateMock = $this->getMock(
            'Magento\Core\Model\Mview\View\State',
            array('getViewId', 'loadByView', 'getVersionId', 'setVersionId', 'getUpdated',
                'getStatus', 'setStatus', 'getMode', 'setMode', 'save', '__wakeup'),
            array(),
            '',
            false
        );
        $this->changelogMock = $this->getMock(
            'Magento\Mview\View\Changelog',
            array('getViewId', 'setViewId', 'create', 'drop', 'getVersion', 'getList', 'clear'),
            array(),
            '',
            false
        );
        $this->subscriptionFactoryMock = $this->getMock(
            'Magento\Mview\View\SubscriptionFactory', array('create'), array(), '', false
        );
        $this->model = new View(
            $this->configMock,
            $this->actionFactoryMock,
            $this->stateMock,
            $this->changelogMock,
            $this->subscriptionFactoryMock
        );
    }

    public function testGetActionClass()
    {
        $this->model->setData('action_class', 'actionClass');
        $this->assertEquals('actionClass', $this->model->getActionClass());
    }

    public function testGetGroup()
    {
        $this->model->setData('group', 'some_group');
        $this->assertEquals('some_group', $this->model->getGroup());
    }

    public function testGetSubscriptions()
    {
        $this->model->setData('subscriptions', ['subscription']);
        $this->assertEquals(['subscription'], $this->model->getSubscriptions());
    }

    public function testLoad()
    {
        $viewId = 'view_test';
        $this->configMock->expects($this->once())
            ->method('getView')
            ->with($viewId)
            ->will($this->returnValue($this->getViewData()));
        $this->assertInstanceOf('Magento\Mview\View', $this->model->load($viewId));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage view_id view does not exist.
     */
    public function testLoadWithException()
    {
        $viewId = 'view_id';
        $this->configMock->expects($this->once())
            ->method('getView')
            ->with($viewId)
            ->will($this->returnValue($this->getViewData()));
        $this->model->load($viewId);
    }

    public function testSubscribe()
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('disabled'));
        $this->stateMock->expects($this->once())
            ->method('setMode')
            ->with('enabled')
            ->will($this->returnSelf());
        $this->changelogMock->expects($this->once())
            ->method('create');
        $subscriptionMock = $this->getMock('Magento\Mview\View\Subscription', array('create'), array(), '', false);
        $subscriptionMock->expects($this->exactly(1))
            ->method('create');
        $this->subscriptionFactoryMock->expects($this->exactly(1))
            ->method('create')
            ->will($this->returnValue($subscriptionMock));
        $this->loadView();
        $this->model->subscribe();
    }

    public function testSubscribeEnabled()
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('enabled'));
        $this->stateMock->expects($this->never())
            ->method('setMode');
        $this->changelogMock->expects($this->never())
            ->method('create');
        $this->subscriptionFactoryMock->expects($this->never())
            ->method('create');
        $this->loadView();
        $this->model->subscribe();
    }

    /**
     * @expectedException \Exception
     */
    public function testSubscribeWithException()
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('disabled'));

        $this->changelogMock->expects($this->once())
            ->method('create')
            ->will($this->returnCallback(
                function () {
                    throw new \Exception();
                }
            ));

        $this->loadView();
        $this->model->subscribe();
    }

    public function testUnsubscribe()
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('enabled'));
        $this->stateMock->expects($this->once())
            ->method('setVersionId')
            ->with(null)
            ->will($this->returnSelf());
        $this->stateMock->expects($this->once())
            ->method('setMode')
            ->with('disabled')
            ->will($this->returnSelf());
        $this->changelogMock->expects($this->once())
            ->method('drop');
        $subscriptionMock = $this->getMock('Magento\Mview\View\Subscription', array('remove'), array(), '', false);
        $subscriptionMock->expects($this->exactly(1))
            ->method('remove');
        $this->subscriptionFactoryMock->expects($this->exactly(1))
            ->method('create')
            ->will($this->returnValue($subscriptionMock));
        $this->loadView();
        $this->model->unsubscribe();
    }

    public function testUnsubscribeDisabled()
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('disabled'));
        $this->stateMock->expects($this->never())
            ->method('setVersionId');
        $this->stateMock->expects($this->never())
            ->method('setMode');
        $this->changelogMock->expects($this->never())
            ->method('drop');
        $this->subscriptionFactoryMock->expects($this->never())
            ->method('create');
        $this->loadView();
        $this->model->unsubscribe();
    }

    /**
     * @expectedException \Exception
     */
    public function testUnsubscribeWithException()
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('enabled'));

        $subscriptionMock = $this->getMock('Magento\Mview\View\Subscription', array('remove'), array(), '', false);
        $subscriptionMock->expects($this->exactly(1))
            ->method('remove')
            ->will($this->returnCallback(
                function () {
                    throw new \Exception();
                }
            ));
        $this->subscriptionFactoryMock->expects($this->exactly(1))
            ->method('create')
            ->will($this->returnValue($subscriptionMock));

        $this->loadView();
        $this->model->unsubscribe();
    }

    public function testUpdate()
    {
        $currentVersionId = 3;
        $lastVersionId = 1;
        $listId = array(2, 3);
        $this->stateMock->expects($this->any())
            ->method('getViewId')
            ->will($this->returnValue(1));
        $this->stateMock->expects($this->once())
            ->method('getVersionId')
            ->will($this->returnValue($lastVersionId));
        $this->stateMock->expects($this->once())
            ->method('setVersionId')
            ->will($this->returnSelf());
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('enabled'));
        $this->stateMock->expects($this->exactly(2))
            ->method('getStatus')
            ->will($this->returnValue('idle'));
        $this->stateMock->expects($this->exactly(2))
            ->method('setStatus')
            ->will($this->returnSelf());
        $this->stateMock->expects($this->exactly(2))
            ->method('save')
            ->will($this->returnSelf());

        $this->changelogMock->expects($this->once())
            ->method('getVersion')
            ->will($this->returnValue($currentVersionId));
        $this->changelogMock->expects($this->once())
            ->method('getList')
            ->with($lastVersionId, $currentVersionId)
            ->will($this->returnValue($listId));

        $actionMock = $this->getMock('Magento\Mview\Action', array('execute'), array(), '', false);
        $actionMock->expects($this->once())
            ->method('execute')
            ->with($listId)
            ->will($this->returnSelf());
        $this->actionFactoryMock->expects($this->once())
            ->method('get')
            ->with('Some\Class\Name')
            ->will($this->returnValue($actionMock));

        $this->loadView();
        $this->model->update();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception
     */
    public function testUpdateWithException()
    {
        $currentVersionId = 3;
        $lastVersionId = 1;
        $listId = array(2, 3);
        $this->stateMock->expects($this->any())
            ->method('getViewId')
            ->will($this->returnValue(1));
        $this->stateMock->expects($this->once())
            ->method('getVersionId')
            ->will($this->returnValue($lastVersionId));
        $this->stateMock->expects($this->never())
            ->method('setVersionId');
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('enabled'));
        $this->stateMock->expects($this->exactly(2))
            ->method('getStatus')
            ->will($this->returnValue('idle'));
        $this->stateMock->expects($this->exactly(2))
            ->method('setStatus')
            ->will($this->returnSelf());
        $this->stateMock->expects($this->exactly(2))
            ->method('save')
            ->will($this->returnSelf());

        $this->changelogMock->expects($this->once())
            ->method('getVersion')
            ->will($this->returnValue($currentVersionId));
        $this->changelogMock->expects($this->once())
            ->method('getList')
            ->with($lastVersionId, $currentVersionId)
            ->will($this->returnValue($listId));

        $actionMock = $this->getMock('Magento\Mview\Action', array('execute'), array(), '', false);
        $actionMock->expects($this->once())
            ->method('execute')
            ->with($listId)
            ->will($this->returnCallback(function () {
                throw new \Exception('Test exception');
            }));
        $this->actionFactoryMock->expects($this->once())
            ->method('get')
            ->with('Some\Class\Name')
            ->will($this->returnValue($actionMock));

        $this->loadView();
        $this->model->update();
    }

    public function testSuspend()
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('enabled'));
        $this->stateMock->expects($this->once())
            ->method('setVersionId')
            ->with(11)
            ->will($this->returnSelf());
        $this->stateMock->expects($this->once())
            ->method('setStatus')
            ->with('suspended')
            ->will($this->returnSelf());
        $this->stateMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());

        $this->changelogMock->expects($this->once())
            ->method('getVersion')
            ->will($this->returnValue(11));

        $this->loadView();
        $this->model->suspend();
    }

    public function testSuspendDisabled()
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('disabled'));
        $this->stateMock->expects($this->never())
            ->method('setVersionId');
        $this->stateMock->expects($this->never())
            ->method('setStatus');
        $this->stateMock->expects($this->never())
            ->method('save');

        $this->changelogMock->expects($this->never())
            ->method('getVersion');

        $this->loadView();
        $this->model->suspend();
    }

    public function testResume()
    {
        $this->stateMock->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue('suspended'));
        $this->stateMock->expects($this->once())
            ->method('setStatus')
            ->with('idle')
            ->will($this->returnSelf());
        $this->stateMock->expects($this->once())
            ->method('save')
            ->will($this->returnSelf());

        $this->loadView();
        $this->model->resume();
    }

    /**
     * @param string $status
     * @dataProvider dataProviderResumeNotSuspended
     */
    public function testResumeNotSuspended($status)
    {
        $this->stateMock->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue($status));
        $this->stateMock->expects($this->never())
            ->method('setStatus');
        $this->stateMock->expects($this->never())
            ->method('save');

        $this->loadView();
        $this->model->resume();
    }

    public function dataProviderResumeNotSuspended()
    {
        return [
            ['idle'],
            ['working'],
        ];
    }

    public function testClearChangelog()
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('enabled'));
        $this->stateMock->expects($this->once())
            ->method('getVersionId')
            ->will($this->returnValue(11));
        $this->changelogMock->expects($this->once())
            ->method('clear')
            ->with(11)
            ->will($this->returnValue(true));
        $this->loadView();
        $this->model->clearChangelog();
    }

    public function testClearChangelogDisabled()
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('disabled'));
        $this->stateMock->expects($this->never())
            ->method('getVersionId');
        $this->changelogMock->expects($this->never())
            ->method('clear');
        $this->loadView();
        $this->model->clearChangelog();
    }

    public function testSetState()
    {
        $this->model->setState($this->stateMock);
        $this->assertEquals($this->stateMock, $this->model->getState());
    }

    /**
     * @param string $mode
     * @param bool $result
     * @dataProvider dataProviderIsEnabled
     */
    public function testIsEnabled($mode, $result)
    {
        $this->stateMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));
        $this->assertEquals($result, $this->model->isEnabled());
    }

    public function dataProviderIsEnabled()
    {
        return [
            ['enabled', true],
            ['disabled', false],
        ];
    }

    /**
     * @param string $status
     * @param bool $result
     * @dataProvider dataProviderIsIdle
     */
    public function testIsIdle($status, $result)
    {
        $this->stateMock->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue($status));
        $this->assertEquals($result, $this->model->isIdle());
    }

    public function dataProviderIsIdle()
    {
        return [
            ['idle', true],
            ['working', false],
            ['suspended', false],
        ];
    }

    /**
     * @param string $status
     * @param bool $result
     * @dataProvider dataProviderIsWorking
     */
    public function testIsWorking($status, $result)
    {
        $this->stateMock->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue($status));
        $this->assertEquals($result, $this->model->isWorking());
    }

    public function dataProviderIsWorking()
    {
        return [
            ['idle', false],
            ['working', true],
            ['suspended', false],
        ];
    }

    /**
     * @param string $status
     * @param bool $result
     * @dataProvider dataProviderIsSuspended
     */
    public function testIsSuspended($status, $result)
    {
        $this->stateMock->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue($status));
        $this->assertEquals($result, $this->model->isSuspended());
    }

    public function dataProviderIsSuspended()
    {
        return [
            ['idle', false],
            ['working', false],
            ['suspended', true],
        ];
    }

    public function testGetUpdated()
    {
        $this->stateMock->expects($this->once())
            ->method('getUpdated')
            ->will($this->returnValue('some datetime'));
        $this->assertEquals('some datetime', $this->model->getUpdated());
    }

    protected function loadView()
    {
        $viewId = 'view_test';
        $this->configMock->expects($this->once())
            ->method('getView')
            ->with($viewId)
            ->will($this->returnValue($this->getViewData()));
        $this->model->load($viewId);
    }

    protected function getViewData()
    {
        return array(
            'view_id' => 'view_test',
            'action_class' => 'Some\Class\Name',
            'group' => 'some_group',
            'subscriptions' => array(
                'some_entity' => array(
                    'name' => 'some_entity',
                    'column' => 'entity_id',
                ),
            ),
        );
    }
}
