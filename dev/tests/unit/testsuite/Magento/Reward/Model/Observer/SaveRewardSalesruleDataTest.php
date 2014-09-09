<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class SaveRewardSalesruleDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardResourceFactoryMock;

    /**
     * @var \Magento\Reward\Model\Observer\SaveRewardSalesruleData
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->rewardResourceFactoryMock = $this->getMock(
            '\Magento\Reward\Model\Resource\RewardFactory',
            ['create', '__wakeup'],
            [],
            '',
            false
        );
        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\SaveRewardSalesruleData',
            [
                'rewardResourceFactory' => $this->rewardResourceFactoryMock,
                'rewardData' => $this->rewardDataMock
            ]
        );
    }

    public function testRewardPointsForSalesRuleWhenRewardsDisabled()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testRewardPointsForSalesRule()
    {
        $ruleId = 1;
        $pointsBalance = 100;

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));

        $ruleMock = $this->getMock(
            '\Magento\SalesRule\Model\Rule',
            ['getRewardPointsDelta', 'getId', '__wakeup'],
            [],
            '',
            false
        );
        $ruleMock->expects($this->once())->method('getId')->will($this->returnValue($ruleId));
        $ruleMock->expects($this->once())->method('getRewardPointsDelta')->will($this->returnValue($pointsBalance));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getRule'], [], '', false);
        $eventMock->expects($this->once())->method('getRule')->will($this->returnValue($ruleMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $rewardMock = $this->getMock(
            '\Magento\Reward\Model\Reward',
            ['saveRewardSalesrule', '__wakeup'],
            [],
            '',
            false
        );
        $this->rewardResourceFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($rewardMock));

        $rewardMock->expects($this->once())
            ->method('saveRewardSalesrule')
            ->with($ruleId, $pointsBalance)
            ->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
 