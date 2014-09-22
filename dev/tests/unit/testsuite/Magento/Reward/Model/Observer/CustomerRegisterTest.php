<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class CustomerRegisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Reward\Model\Observer\CustomerRegister
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\RewardFactory', ['create'], [], '', false);
        $this->loggerMock = $this->getMock('\Magento\Framework\Logger', [], [], '', false);

        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\CustomerRegister',
            [
                'rewardData' => $this->rewardDataMock,
                'storeManager' => $this->storeManagerMock,
                'rewardFactory' => $this->rewardFactoryMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    public function testUpdateRewardPointsWhenRewardDisabledInFront()
    {
        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue(false));
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardPointsWhenCustomerHasOrigData()
    {
        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue(true));
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);

        $customerMock = $this->getMock('\Magento\Customer\Model\Customer', ['getOrigData', '__wakeup'], [], '', false);
        $customerMock->expects($this->once())->method('getOrigData')->will($this->returnValue(['origData']));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getCustomer'], [], '', false);
        $eventMock->expects($this->once())->method('getCustomer')->will($this->returnValue($customerMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardPointsSuccess()
    {
        $websiteId = 1;
        $storeId = 2;
        $notificationConfig = 100;
        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue(true));
        $this->rewardDataMock->expects($this->once())
            ->method('getNotificationConfig')
            ->with('subscribe_by_default', $websiteId)
            ->will($this->returnValue($notificationConfig));

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $customerMock = $this->getMock(
            '\Magento\Customer\Model\Customer',
            ['getOrigData', '__wakeup', 'setRewardUpdateNotification', 'setRewardWarningNotification', 'getResource'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getCustomer'], [], '', false);
        $eventMock->expects($this->once())->method('getCustomer')->will($this->returnValue($customerMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $customerMock->expects($this->once())->method('getOrigData')->will($this->returnValue(null));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock->expects($this->exactly(2))->method('getStore')->will($this->returnValue($storeMock));

        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));

        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['setCustomer', 'setActionEntity', 'setStore', 'setAction', 'updateRewardPoints', '__wakeup'],
            [],
            '',
            false
        );
        $this->rewardFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rewardMock));

        $rewardMock->expects($this->once())->method('setCustomer')->with($customerMock)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setActionEntity')->with($customerMock)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setStore')->with($storeId)->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setAction')
            ->with(\Magento\Reward\Model\Reward::REWARD_ACTION_REGISTER)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('updateRewardPoints')->will($this->returnSelf());

        $customerMock->expects($this->once())
            ->method('setRewardUpdateNotification')
            ->with($notificationConfig)
            ->will($this->returnSelf());
        $customerMock->expects($this->once())
            ->method('setRewardWarningNotification')
            ->with($notificationConfig)
            ->will($this->returnSelf());

        $customerResourceMock = $this->getMock(
            'Magento\Customer\Model\Resource\Customer',
            ['saveAttribute', '__wakeup'],
            [],
            '',
            false
        );
        $customerMock->expects($this->exactly(2))
            ->method('getResource')
            ->will($this->returnValue($customerResourceMock));

        $valueMap = [
            [$customerMock, 'reward_update_notification', $customerResourceMock],
            [$customerMock, 'reward_warning_notification', $customerResourceMock]
        ];

        $customerResourceMock->expects($this->exactly(2))
            ->method('saveAttribute')
            ->will($this->returnValueMap($valueMap));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardsThrowsException()
    {
        $websiteId = 1;
        $storeId = 2;
        $notificationConfig = 100;
        $this->rewardDataMock->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue(true));
        $this->rewardDataMock->expects($this->once())
            ->method('getNotificationConfig')
            ->with('subscribe_by_default', $websiteId)
            ->will($this->returnValue($notificationConfig));

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $customerMock = $this->getMock('\Magento\Customer\Model\Customer', ['getOrigData', '__wakeup'], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getCustomer'], [], '', false);
        $eventMock->expects($this->once())->method('getCustomer')->will($this->returnValue($customerMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $customerMock->expects($this->once())->method('getOrigData')->will($this->returnValue(null));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock->expects($this->exactly(2))->method('getStore')->will($this->returnValue($storeMock));

        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));

        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['setCustomer', 'setActionEntity', 'setStore', 'setAction', 'updateRewardPoints', '__wakeup'],
            [],
            '',
            false
        );
        $this->rewardFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rewardMock));

        $rewardMock->expects($this->once())->method('setCustomer')->with($customerMock)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setActionEntity')->with($customerMock)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setStore')->with($storeId)->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setAction')
            ->with(\Magento\Reward\Model\Reward::REWARD_ACTION_REGISTER)
            ->will($this->returnSelf());

        $exceptionMock = new \Exception();
        $rewardMock->expects($this->once())->method('updateRewardPoints')->will($this->throwException($exceptionMock));

        $this->loggerMock->expects($this->once())->method('logException')->with($exceptionMock);

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
