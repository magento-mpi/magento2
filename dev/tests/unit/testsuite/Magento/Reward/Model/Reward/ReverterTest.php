<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Reward;

class ReverterTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Reward\Model\Reward\Reverter
     */
    protected $reverter;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\RewardFactory', ['create'], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->reverter = $objectManager->getObject(
            '\Magento\Reward\Model\Reward\Reverter',
            [
                'rewardFactory' => $this->rewardFactoryMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    public function testRevertRewardPointsForOrderPositive()
    {
        $customerId = 1;
        $storeId = 2;
        $websiteId = 100;

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['__wakeup', 'getCustomerId', 'getStoreId', 'getRewardPointsBalance'],
            [],
            '',
            false
        );

        $storeMock = $this->getMock('\Magento\Store\Model\Store', ['getWebsiteId', '__wakeup'], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->will($this->returnValue($storeMock));

        $rewardMock = $this->getMock('\Magento\Reward\Model\Reward',
            [
                '__wakeup',
                'setCustomerId',
                'setWebsiteId',
                'setPointsDelta',
                'setAction',
                'setActionEntity',
                'updateRewardPoints'
            ],
            [],
            '',
            false
        );
        $this->rewardFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rewardMock));

        $rewardMock->expects($this->once())->method('setCustomerId')->with($customerId)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setWebsiteId')->with($websiteId)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setPointsDelta')->with(500)->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setAction')
            ->with(\Magento\Reward\Model\Reward::REWARD_ACTION_REVERT)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setActionEntity')->with($orderMock)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('updateRewardPoints')->will($this->returnSelf());

        $orderMock->expects($this->exactly(2))->method('getCustomerId')->will($this->returnValue($customerId));
        $orderMock->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));
        $orderMock->expects($this->once())->method('getRewardPointsBalance')->will($this->returnValue(500));

        $this->assertEquals($this->reverter, $this->reverter->revertRewardPointsForOrder($orderMock));
    }

    public function testRevertRewardPointsIfNoCustomerId()
    {
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', ['__wakeup', 'getCustomerId'], [], '', false);
        $orderMock->expects($this->once())->method('getCustomerId')->will($this->returnValue(null));
        $this->assertEquals($this->reverter, $this->reverter->revertRewardPointsForOrder($orderMock));
    }
}
