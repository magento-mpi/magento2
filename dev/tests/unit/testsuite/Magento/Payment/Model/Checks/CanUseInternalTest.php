<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Checks;

class CanUseInternalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CanUseInternal
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new CanUseInternal();
    }

    /**
     * @dataProvider paymentMethodDataProvider
     * @param PaymentMethodChecksInterface $paymentMethod Prepared payment method
     * @param \Magento\Sales\Model\Quote $quote  Is not used in the method
     * @param bool $expectation
     */
    public function testIsApplicable($paymentMethod, $quote, $expectation)
    {
        $this->assertEquals($expectation, $this->_model->isApplicable($paymentMethod, $quote));
    }

    /**
     * Returns array of two prepared payments(first can use checkout, second can't use)
     *
     * @return array
     */
    public function paymentMethodDataProvider()
    {
        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $paymentMethodCanUseCheckout = $this->_getPaymentMethod();
        $paymentMethodCanUseCheckout->expects($this->once())->method('canUseInternal')->will($this->returnValue(true));
        $paymentMethodCantUseCheckout = $this->_getPaymentMethod();
        $paymentMethodCantUseCheckout->expects($this->once())->method('canUseInternal')->will(
            $this->returnValue(false)
        );
        return [
            [$paymentMethodCanUseCheckout, $quoteMock, true],
            [$paymentMethodCantUseCheckout, $quoteMock, false]
        ];
    }

    /**
     * Returns PaymentMethodChecksInterface mock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getPaymentMethod()
    {
        return $this->getMockBuilder(
            'Magento\Payment\Model\Checks\PaymentMethodChecksInterface'
        )->disableOriginalConstructor()->setMethods([])->getMock();

    }
}
