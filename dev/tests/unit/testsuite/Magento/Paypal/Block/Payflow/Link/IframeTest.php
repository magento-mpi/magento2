<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Payflow\Link;

/**
 * Test for Iframe block
 *
 * @package Magento\Paypal\Block\Payflow\Link
 */
class IframeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check that isScopePrivate is false
     */
    public function testCheckIsScopePrivate()
    {
        $contextMock = $this->getMock('Magento\View\Element\Template\Context', [], [], '', false);
        $checkoutSessionMock = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);
        $orderFactoryMock = $this->getMock('Magento\Sales\Model\OrderFactory', ['getQuote'], [], '', false);
        $hssHelperMock = $this->getMock('Magento\Paypal\Helper\Hss', [], [], '', false);
        $paymentDataMock = $this->getMock('Magento\Payment\Helper\Data', [], [], '', false);
        $quoteMock = $this->getMock('Magento\Sales\Model\Quote', ['getPayment', '__wakeup'], [], '', false);
        $paymentMock = $this->getMock('Magento\Sales\Model\Quote\Payment', [], [], '', false);

        $checkoutSessionMock->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));
        $quoteMock->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($paymentMock));
        $hssHelperMock->expects($this->any())
            ->method('getHssMethods')
            ->will($this->returnValue([]));

        $block = new \Magento\Paypal\Block\Payflow\Advanced\Iframe(
            $contextMock,
            $orderFactoryMock,
            $checkoutSessionMock,
            $hssHelperMock,
            $paymentDataMock
        );

        $this->assertFalse($block->isScopePrivate());
    }
}
