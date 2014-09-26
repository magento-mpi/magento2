<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class QuoteCollectTotalsBeforeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Observer\QuoteCollectTotalsBefore
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\QuoteCollectTotalsBefore');
    }

    public function testSetFlagToResetRewardPoints()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $quoteMock = $this->getMock(
            '\Magento\Sales\Model\Quote',
            ['setRewardPointsTotalReseted', '__wakeup'],
            [],
            '',
            false
        );
        $quoteMock->expects($this->once())
            ->method('setRewardPointsTotalReseted')
            ->with(false)
            ->will($this->returnSelf());

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getQuote'], [], '', false);
        $eventMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
 