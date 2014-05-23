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
     * List of quote base grand totals
     *
     * @var array
     */
    private $baseSubtotal = [
        'not_free' => ['total' => 0, 'expectation' => false],
        'free' => ['total' => 1, 'expectation' => true]
    ];

    /**
     * @dataProvider paymentMethodDataProvider
     * @param PaymentMethodChecksInterface $paymentMethod Prepared payment method
     * @param \Magento\Sales\Model\Quote $quote  Is not used in the method
     * @param bool $expectation
     */
    public function testIsApplicable($paymentMethod, $quote, $expectation)
    {
        $model = new ZeroTotal();
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
        foreach ($this->baseSubtotal as $code => $data) {
            $resultArray[] = [$this->_getPaymentMethod($code), $this->_getQuote($data['total']), $data['expectation']];
        }
        return $resultArray;
    }

    /**
     * Returns PaymentMethodChecksInterface mock
     *
     * @param string $code
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getPaymentMethod($code)
    {
        $paymentMethod = $this->getMockBuilder(
            'Magento\Payment\Model\Checks\PaymentMethodChecksInterface'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $paymentMethod->expects($this->once())->method('getCode')->will($this->returnValue($code));

        return $paymentMethod;
    }

    /**
     * Return prepared quote with base grand total value
     *
     * @param int $baseSubtotal
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getQuote($baseSubtotal)
    {
        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')->disableOriginalConstructor()->setMethods(
            ['getBaseSubtotal', 'getShippingAddress', '__wakeup']
        )->getMock();
        $shippingAddress = $this->getMockBuilder(
            'Magento\Sales\Model\Quote\Address'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $quoteMock->expects($this->once())->method('getBaseSubtotal')->will($this->returnValue($baseSubtotal));
        $quoteMock->expects($this->once())->method('getShippingAddress')->will($this->returnValue($shippingAddress));
        $shippingAddress->expects($this->once())->method('getBaseShippingAmount')->will(
            $this->returnValue($baseSubtotal)
        );
        return $quoteMock;
    }
}
