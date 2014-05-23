<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Checks;

class TotalMinMaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Payment min total value
     */
    const PAYMENT_MIN_TOTAL = 2;

    /**
     * Payment max total value
     */
    const PAYMENT_MAX_TOTAL = 5;

    /**
     * List of quote base grand totals
     *
     * @var array
     */
    private $quoteTotalsList = [1 => false, 6 => false, 3 => true];

    /**
     * @dataProvider paymentMethodDataProvider
     * @param PaymentMethodChecksInterface $paymentMethod Prepared payment method
     * @param \Magento\Sales\Model\Quote $quote  Is not used in the method
     * @param bool $expectation
     */
    public function testIsApplicable($paymentMethod, $quote, $expectation)
    {
        $model = new TotalMinMax();
        $this->assertEquals($expectation, $model->isApplicable($paymentMethod, $quote));
    }

    /**
     * Returns array of three prepared payments, with different Quote baseGrandTotal value
     *
     * @return array
     */
    public function paymentMethodDataProvider()
    {
        $resultArray = [];
        foreach ($this->quoteTotalsList as $baseGrandTotal => $expectation) {
            $resultArray[] = [$this->_getPaymentMethod(), $this->_getQuote($baseGrandTotal), $expectation];
        }
        return $resultArray;
    }

    /**
     * Returns PaymentMethodChecksInterface mock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getPaymentMethod()
    {
       $paymentMethod = $this->getMockBuilder(
            'Magento\Payment\Model\Checks\PaymentMethodChecksInterface'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $paymentMethod->expects($this->at(0))->method('getConfigData')->with(
            TotalMinMax::MIN_ORDER_TOTAL
        )->will($this->returnValue(self::PAYMENT_MIN_TOTAL));
        $paymentMethod->expects($this->at(1))->method('getConfigData')->with(
            TotalMinMax::MAX_ORDER_TOTAL
        )->will($this->returnValue(self::PAYMENT_MAX_TOTAL));
        return $paymentMethod;
    }

    /**
     * Return prepared quote with base grand total value
     *
     * @param int $baseGrandTotal
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getQuote($baseGrandTotal)
    {
        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')->disableOriginalConstructor()->setMethods(
            ['getBaseGrandTotal', '__wakeup']
        )->getMock();
        $quoteMock->expects($this->once())->method('getBaseGrandTotal')->will($this->returnValue($baseGrandTotal));
        return $quoteMock;
    }
}
