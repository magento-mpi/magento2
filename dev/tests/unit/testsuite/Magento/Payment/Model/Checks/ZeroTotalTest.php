<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Checks;

class ZeroTotalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider paymentMethodDataProvider
     * @param string $code
     * @param int $total
     * @param bool $expectation
     */
    public function testIsApplicable($code, $total, $expectation)
    {
        $paymentMethod = $this->getMockBuilder(
            'Magento\Payment\Model\Checks\PaymentMethodChecksInterface'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        if (!$total) {
            $paymentMethod->expects($this->once())->method('getCode')->will($this->returnValue($code));
        }

        $quote= $this->getMockBuilder('Magento\Sales\Model\Quote')->disableOriginalConstructor()->setMethods(
            ['getBaseSubtotal', 'getShippingAddress', '__wakeup']
        )->getMock();
        $shippingAddress = $this->getMockBuilder(
            'Magento\Sales\Model\Quote\Address'
        )->disableOriginalConstructor()->setMethods(['getBaseShippingAmount', '__wakeup'])->getMock();
        $shippingAddress->expects($this->once())->method('getBaseShippingAmount')->will(
            $this->returnValue($total)
        );
        $quote->expects($this->once())->method('getBaseSubtotal')->will($this->returnValue($total));
        $quote->expects($this->once())->method('getShippingAddress')->will($this->returnValue($shippingAddress));

        $model = new ZeroTotal();
        $this->assertEquals($expectation, $model->isApplicable($paymentMethod, $quote));
    }

    /**
     * @return array
     */
    public function paymentMethodDataProvider()
    {
        return [['not_free', 0, false], ['free', 1, true]];
    }
}
