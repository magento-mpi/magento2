<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Model\Observer;

class ReviewSubmitTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Reward\Model\Observer\ReviewSubmit
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\RewardFactory', ['create'], [], '', false);
        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);

        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\ReviewSubmit',
            [
                'storeManager' => $this->storeManagerMock,
                'rewardFactory' => $this->rewardFactoryMock,
                'rewardData' => $this->rewardDataMock
            ]
        );
    }

    public function testUpdateRewardPointsWhenRewardDisabledInFront()
    {
        $websiteId = 2;

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $reviewMock = $this->getMock('\Magento\Review\Model\Review', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getObject'], [], '', false);
        $eventMock->expects($this->once())->method('getObject')->will($this->returnValue($reviewMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(false));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardPointsIfReviewNotApproved()
    {
        $websiteId = 2;

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $reviewMock = $this->getMock('\Magento\Review\Model\Review', [], [], '', false);
        $reviewMock->expects($this->once())->method('isApproved')->will($this->returnValue(false));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getObject'], [], '', false);
        $eventMock->expects($this->once())->method('getObject')->will($this->returnValue($reviewMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(true));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardPointsIfCustomerIdNotSet()
    {
        $websiteId = 2;

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $reviewMock = $this->getMock(
            '\Magento\Review\Model\Review',
            ['getCustomerId', 'isApproved', '__wakeup'],
            [],
            '',
            false
        );
        $reviewMock->expects($this->once())->method('isApproved')->will($this->returnValue(true));
        $reviewMock->expects($this->once())->method('getCustomerId')->will($this->returnValue(null));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getObject'], [], '', false);
        $eventMock->expects($this->once())->method('getObject')->will($this->returnValue($reviewMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(true));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testUpdateRewardPoints()
    {
        $storeId = 1;
        $websiteId = 2;
        $customerId = 100;

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $reviewMock = $this->getMock(
            '\Magento\Review\Model\Review',
            ['getCustomerId', 'isApproved', '__wakeup', 'getStoreId'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getObject'], [], '', false);
        $eventMock->expects($this->once())->method('getObject')->will($this->returnValue($reviewMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getWebsiteId')->will($this->returnValue($websiteId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->rewardDataMock->expects($this->once())
            ->method('isEnabledOnFront')
            ->with($websiteId)
            ->will($this->returnValue(true));

        $reviewMock->expects($this->once())->method('isApproved')->will($this->returnValue(true));
        $reviewMock->expects($this->exactly(2))->method('getCustomerId')->will($this->returnValue($customerId));
        $reviewMock->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue($storeId));

        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['setCustomerId', 'setActionEntity', 'setStore', 'setAction', 'updateRewardPoints', '__wakeup'],
            [],
            '',
            false
        );
        $this->rewardFactoryMock->expects($this->once())->method('create')->will($this->returnValue($rewardMock));

        $rewardMock->expects($this->once())->method('setCustomerId')->with($customerId)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setStore')->with($storeId)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setActionEntity')->with($reviewMock)->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('setAction')
            ->with(\Magento\Reward\Model\Reward::REWARD_ACTION_REVIEW)
            ->will($this->returnSelf());
        $rewardMock->expects($this->once())->method('updateRewardPoints')->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
