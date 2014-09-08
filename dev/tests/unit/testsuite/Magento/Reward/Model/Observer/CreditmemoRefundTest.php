<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class CreditmemoRefundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Observer\CreditmemoRefund
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\CreditmemoRefund', []);
    }

    public function testCreditmemoRefund()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $creditmemoMock = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            ['getBaseRewardCurrencyAmount', '__wakeup', 'getOrder'],
            [],
            '',
            false
        );
        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['getBaseRwrdCrrncyAmntRefnded', 'getBaseRwrdCrrncyAmtInvoiced', '__wakeup', 'setForcedCanCreditmemo'],
            [],
            '',
            false
        );

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getCreditmemo'], [], '', false);
        $eventMock->expects($this->exactly(2))->method('getCreditmemo')->will($this->returnValue($creditmemoMock));

        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));
        $creditmemoMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));

        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmntRefnded')->will($this->returnValue(10));
        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmtInvoiced')->will($this->returnValue(25));
        $orderMock->expects($this->once())->method('setForcedCanCreditmemo')->with(false)->will($this->returnSelf());

        $creditmemoMock->expects($this->once())->method('getBaseRewardCurrencyAmount')->will($this->returnValue(15));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
