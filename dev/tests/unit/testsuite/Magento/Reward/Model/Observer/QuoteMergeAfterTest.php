<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class QuoteMergeAfterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Observer\QuoteMergeAfter
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\QuoteMergeAfter');
    }

    public function testSetFlagToResetRewardPoints()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            ['setUseRewardPoints', '__wakeup'],
            [],
            '',
            false
        );
        $quoteMock->expects($this->once())
            ->method('setUseRewardPoints')
            ->with(true)
            ->will($this->returnSelf());

        $sourceMock = $this->getMock('\Magento\Framework\Object', ['getUseRewardPoints'], [], '', false);
        $sourceMock->expects($this->exactly(2))->method('getUseRewardPoints')->will($this->returnValue(true));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getQuote', 'getSource'], [], '', false);
        $eventMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));
        $eventMock->expects($this->once())->method('getSource')->will($this->returnValue($sourceMock));
        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetFlagToResetRewardPointsIfRewardPointsIsNull()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);

        $sourceMock = $this->getMock('\Magento\Framework\Object', ['getUseRewardPoints'], [], '', false);
        $sourceMock->expects($this->once())->method('getUseRewardPoints')->will($this->returnValue(false));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getQuote', 'getSource'], [], '', false);
        $eventMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));
        $eventMock->expects($this->once())->method('getSource')->will($this->returnValue($sourceMock));
        $observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
 