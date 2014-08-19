<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Model\Reward\Refund;

class SalesRuleRefundTest extends \PHPUnit_Framework_TestCase
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
    protected $rewardHelperMock;

    /**
     * @var \Magento\Reward\Model\Reward\Refund\SalesRuleRefund
     */
    protected $subject;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\RewardFactory',
            ['create', '__wakeup'],
            [],
            '',
            false
        );
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface', [], [], '', false);
        $this->rewardHelperMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);

        $this->subject = $this->objectManager->getObject(
            '\Magento\Reward\Model\Reward\Refund\SalesRuleRefund',
            [
                'rewardFactory' => $this->rewardFactoryMock,
                'storeManager' => $this->storeManagerMock,
                'rewardHelper' => $this->rewardHelperMock
            ]
        );
    }

    public function testRefund()
    {
        $creditmemoTotalQty = 5;
        $orderMock = $this->getMock('\Magento\Sales\Model\Order',
            [
                'getRewardSalesrulePoints',
                '__wakeup',
                'getCreditmemosCollection',
                'getTotalQtyOrdered',
                'getStoreId',
                'getCustomerId'
            ],
            [],
            '',
            false
        );

        $creditmemoMock = $this->getMock('\Magento\Sales\Model\Order\Creditmemo',
            [
                'getTotalQty',
                '__wakeup',
                'getOrder',
                'getAutomaticallyCreated',
                'setRewardPointsBalanceRefund',
                'getRewardPointsBalance'
            ],
            [],
            '',
            false
        );
        $creditmemoMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $creditmemoMock->expects($this->atLeastOnce())
            ->method('getTotalQty')
            ->will($this->returnValue($creditmemoTotalQty));

        $creditmemo = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            ['getSelections', '__wakeup', 'getData', 'getAllItems'],
            [],
            '',
            false
        );
        $creditmemos = [$creditmemo];
        $creditmemoCollectionMock = $this->objectManager->getCollectionMock(
            '\Magento\Sales\Model\Resource\Order\Creditmemo\Collection',
            $creditmemos
        );

        $orderMock->expects($this->atLeastOnce())
            ->method('getCreditmemosCollection')
            ->will($this->returnValue($creditmemoCollectionMock));

        $itemMock = $this->getMock('\Magento\Sales\Model\Order\Creditmemo\Item', ['getQty', '__wakeup'], [], '', false);
        $creditmemo->expects($this->atLeastOnce())->method('getAllItems')->will($this->returnValue([$itemMock]));

        $itemMock->expects($this->atLeastOnce())->method('getQty')->will($this->returnValue(5));

        $creditmemoMock->expects($this->once())->method('getAutomaticallyCreated')->will($this->returnValue(true));

        $this->rewardHelperMock->expects($this->once())->method('isAutoRefundEnabled')->will($this->returnValue(true));

        $creditmemoMock->expects($this->once())->method('getRewardPointsBalance')->will($this->returnValue(100));
        $creditmemoMock->expects($this->once())
            ->method('setRewardPointsBalanceRefund')
            ->with(100)
            ->will($this->returnSelf());

        $orderMock->expects($this->exactly(3))->method('getRewardSalesrulePoints')->will($this->returnValue(200));
        $orderMock->expects($this->once())->method('getTotalQtyOrdered')->will($this->returnValue(10));

        $rewardMock = $this->getMock('\Magento\Reward\Model\Reward',
            [
                'setCustomerId',
                '__wakeup',
                'setWebsiteId',
                'getPointsBalance',
                'setPointsDelta',
                'setAction',
                'setActionEntity',
                'loadByCustomer',
                'save'
            ],
            [],
            '',
            false
        );
        $this->rewardFactoryMock->expects($this->exactly(2))->method('create')->will($this->returnValue($rewardMock));

        $orderMock->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue(1));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->storeManagerMock->expects($this->exactly(2))->method('getStore')->will($this->returnValue($storeMock));
        $storeMock->expects($this->exactly(2))->method('getWebsiteId')->will($this->returnValue(2));

        $rewardMock->expects($this->exactly(2))
            ->method('setWebsiteId')
            ->with(2)
            ->will($this->returnSelf());

        $rewardMock->expects($this->exactly(2))
            ->method('setCustomerId')
            ->with(10)
            ->will($this->returnSelf());

        $orderMock->expects($this->exactly(2))->method('getCustomerId')->will($this->returnValue(10));

        $rewardMock->expects($this->once())->method('loadByCustomer')->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('getPointsBalance')->will($this->returnValue(500));
        $rewardMock->expects($this->once())
            ->method('setPointsDelta')
            ->with(-200)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setAction')
            ->with(\Magento\Reward\Model\Reward::REWARD_ACTION_CREDITMEMO_VOID)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())
            ->method('setActionEntity')
            ->with($orderMock)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('save')->will($this->returnSelf());

        $this->subject->refund($creditmemoMock);
    }
}
 