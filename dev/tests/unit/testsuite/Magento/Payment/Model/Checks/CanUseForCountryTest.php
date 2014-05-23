<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Checks;

class CanUseForCountryTest extends \PHPUnit_Framework_TestCase
{
    const EXPECTED_COUNTRY_ID = 1;

    /**
     * @var CanUseForCountry
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new CanUseForCountry();
    }

    /**
     * @dataProvider paymentMethodDataProvider
     * @param PaymentMethodChecksInterface $paymentMethod Prepared payment method
     * @param \Magento\Sales\Model\Quote $quote
     * @param bool $expectation
     */
    public function testIsApplicable($paymentMethod, $quote, $expectation)
    {
        $this->assertEquals($expectation, $this->_model->isApplicable($paymentMethod, $quote));
    }

    /**
     * Returns array of two prepared payments(first can use for country, second can't use)
     *
     * @return array
     */
    public function paymentMethodDataProvider()
    {
        $paymentMethodCanUseForCountry = $this->_getPaymentMethod();
        $paymentMethodCanUseForCountry->expects($this->once())->method('canUseForCountry')->with(
            self::EXPECTED_COUNTRY_ID
        )->will($this->returnValue(true));
        $paymentMethodCantUseForCountry = $this->_getPaymentMethod();
        $paymentMethodCantUseForCountry->expects($this->once())->method('canUseForCountry')->with(
            self::EXPECTED_COUNTRY_ID
        )->will($this->returnValue(false));
        return [
            [$paymentMethodCanUseForCountry, $this->_getPreparedQuote(), true],
            [$paymentMethodCantUseForCountry, $this->_getPreparedQuote(), false]
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
     * Returns Quote with Billing Address
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getPreparedQuote()
    {
        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $billingAddressMock = $this->getMockBuilder(
            'Magento\Sales\Model\Quote\Address'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $billingAddressMock->expects($this->once())->method('getCountry')->will(
            $this->returnValue(self::EXPECTED_COUNTRY_ID)
        );
        $quoteMock->expects($this->once())->method('getBillingAddress')->will($this->returnValue($billingAddressMock));
        return $quoteMock;
    }
}
