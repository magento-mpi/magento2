<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Checks;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider paymentMethodDataProvider
     * @param PaymentMethodChecksInterface $paymentMethod Prepared payment method
     * @param \Magento\Sales\Model\Quote $quote  Is not used in the method
     * @param bool $expectation
     */
    public function testIsApplicable($paymentMethod, $quote, $expectation)
    {
        $model = new Composite($this->_getPreparedSpecificationList($expectation, $paymentMethod, $quote));
        $this->assertEquals($expectation, $model->isApplicable($paymentMethod, $quote));
    }

    /**
     * Returns array of two prepared payments(first is Applicable, second is not)
     *
     * @return array
     */
    public function paymentMethodDataProvider()
    {
        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        return [
            [$this->_getPaymentMethod(), $quoteMock, true],
            [$this->_getPaymentMethod(), $quoteMock, false]
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

    /**
     * Returns list of SpecificationInterface with prepared expectations for isApplicable
     *
     * @param bool $isApplicableExpectation an expectation for specification isApplicable method
     * @param PaymentMethodChecksInterface $paymentMethod
     * @param \Magento\Sales\Model\Quote $quote
     * @return SpecificationInterface[]
     */
    private function _getPreparedSpecificationList($isApplicableExpectation, $paymentMethod, $quote)
    {
        $specification = $this->getMockBuilder(
            'Magento\Payment\Model\Checks\SpecificationInterface'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $specification->expects($this->once())->method('isApplicable')->with($paymentMethod, $quote)->will(
            $this->returnValue($isApplicableExpectation)
        );
        return [$specification];
    }
}
