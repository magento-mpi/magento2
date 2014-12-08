<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Plugin;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\RecurringPayment\Block\Plugin\Payment */
    protected $payment;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Checkout\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $sessionMock;

    /** @var \Magento\RecurringPayment\Model\Quote\Filter|\PHPUnit_Framework_MockObject_MockObject */
    protected $filterMock;

    protected function setUp()
    {
        $this->sessionMock = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);
        $this->filterMock = $this->getMock('Magento\RecurringPayment\Model\Quote\Filter');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->payment = $this->objectManagerHelper->getObject(
            'Magento\RecurringPayment\Block\Plugin\Payment',
            ['session' => $this->sessionMock, 'filter' => $this->filterMock]
        );
    }

    public function testAfterGetOptions()
    {
        $quote = $this->getMockBuilder('Magento\Sales\Model\Quote')->disableOriginalConstructor()->getMock();
        $this->sessionMock->expects($this->once())->method('getQuote')->will($this->returnValue($quote));
        $this->filterMock->expects(
            $this->once()
        )->method(
            'hasRecurringItems'
        )->with(
            $quote
        )->will(
            $this->returnValue(true)
        );

        $this->assertArrayHasKey(
            'hasRecurringItems',
            $this->payment->afterGetOptions(
                $this->getMock('\Magento\Checkout\Block\Onepage\Payment', [], [], '', false),
                []
            )
        );
    }
}
