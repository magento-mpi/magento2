<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Checks;

class CanUseForCurrencyTest extends \PHPUnit_Framework_TestCase
{
    const EXPECTED_CURRENCY_CODE = 'US';

    /**
     * @var CanUseForCurrency
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new CanUseForCurrency();
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
     * Returns array of two prepared payments(first can use for currency, second can't use)
     *
     * @return array
     */
    public function paymentMethodDataProvider()
    {
        $paymentMethodCanUseForCurrency = $this->_getPaymentMethod();
        $paymentMethodCanUseForCurrency->expects($this->once())->method('canUseForCurrency')->with(
            self::EXPECTED_CURRENCY_CODE
        )->will($this->returnValue(true));
        $paymentMethodCantUseForCurrency = $this->_getPaymentMethod();
        $paymentMethodCantUseForCurrency->expects($this->once())->method('canUseForCurrency')->with(
            self::EXPECTED_CURRENCY_CODE
        )->will($this->returnValue(false));
        return [
            [$paymentMethodCanUseForCurrency, $this->_getPreparedQuote(), true],
            [$paymentMethodCantUseForCurrency, $this->_getPreparedQuote(), false]
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
     * Returns Quote with Store
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getPreparedQuote()
    {
        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $store = $this->getMockBuilder(
            'Magento\Store\Model\Store'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $store->expects($this->once())->method('getBaseCurrencyCode')->will(
            $this->returnValue(self::EXPECTED_CURRENCY_CODE)
        );
        $quoteMock->expects($this->once())->method('getStore')->will($this->returnValue($store));
        return $quoteMock;
    }
}
