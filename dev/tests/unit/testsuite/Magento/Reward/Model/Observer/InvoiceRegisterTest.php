<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class InvoiceRegisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Observer\InvitationToCustomer
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\InvoiceRegister');
    }

    public function testAddRewardsIfRewardCurrencyAmountIsNull()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $invoiceMock = $this->getMock(
            '\Magento\Sales\Model\Order\Invoice',
            ['getBaseRewardCurrencyAmount', '__wakeup'],
            [],
            '',
            false
        );
        $invoiceMock->expects($this->once())->method('getBaseRewardCurrencyAmount')->will($this->returnValue(null));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInvoice'], [], '', false);
        $eventMock->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testAddRewardsSuccess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent', '__wakeup'], [], '', false);
        $invoiceMock = $this->getMock(
            '\Magento\Sales\Model\Order\Invoice',
            [
                'getBaseRewardCurrencyAmount',
                '__wakeup',
                'getOrder',
                'getRewardCurrencyAmount'
            ],
            [],
            '',
            false
        );
        $invoiceMock->expects($this->exactly(2))->method('getBaseRewardCurrencyAmount')->will($this->returnValue(100));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getInvoice'], [], '', false);
        $eventMock->expects($this->once())->method('getInvoice')->will($this->returnValue($invoiceMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            [
                'getRwrdCurrencyAmountInvoiced',
                'getBaseRwrdCrrncyAmtInvoiced',
                'setRwrdCurrencyAmountInvoiced',
                'setBaseRwrdCrrncyAmtInvoiced',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $orderMock->expects($this->once())->method('getRwrdCurrencyAmountInvoiced')->will($this->returnValue(50));
        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmtInvoiced')->will($this->returnValue(50));
        $orderMock->expects($this->once())
            ->method('setRwrdCurrencyAmountInvoiced')
            ->with(100)
            ->will($this->returnSelf());
        $orderMock->expects($this->once())
            ->method('setBaseRwrdCrrncyAmtInvoiced')
            ->with(150)
            ->will($this->returnSelf());

        $invoiceMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));
        $invoiceMock->expects($this->once())->method('getRewardCurrencyAmount')->will($this->returnValue(50));

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
 