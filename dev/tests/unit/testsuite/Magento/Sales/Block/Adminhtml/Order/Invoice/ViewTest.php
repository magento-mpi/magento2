<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Sales\Block\Adminhtml\Order\Invoice\View
 */
namespace Magento\Sales\Block\Adminhtml\Order\Invoice;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param bool $canReviewPayment
     * @param bool $canFetchUpdate
     * @param bool $expectedResult
     * @dataProvider isPaymentReviewDataProvider
     */
    public function testIsPaymentReview($canReviewPayment, $canFetchUpdate, $expectedResult)
    {
        // Create order mock
        $order = $this->getMockBuilder('Magento\Sales\Model\Order')->disableOriginalConstructor()->getMock();
        $order->expects($this->any())->method('canReviewPayment')->will($this->returnValue($canReviewPayment));
        $order->expects(
            $this->any()
        )->method(
            'canFetchPaymentReviewUpdate'
        )->will(
            $this->returnValue($canFetchUpdate)
        );

        // Create invoice mock
        $invoice = $this->getMockBuilder(
            'Magento\Sales\Model\Order\Invoice'
        )->disableOriginalConstructor()->setMethods(
            array('getOrder', '__wakeup')
        )->getMock();
        $invoice->expects($this->once())->method('getOrder')->will($this->returnValue($order));

        // Prepare block to test protected method
        $block = $this->getMockBuilder(
            'Magento\Sales\Block\Adminhtml\Order\Invoice\View'
        )->disableOriginalConstructor()->setMethods(
            array('getInvoice')
        )->getMock();
        $block->expects($this->once())->method('getInvoice')->will($this->returnValue($invoice));
        $testMethod = new \ReflectionMethod('Magento\Sales\Block\Adminhtml\Order\Invoice\View', '_isPaymentReview');
        $testMethod->setAccessible(true);

        $this->assertEquals($expectedResult, $testMethod->invoke($block));
    }

    public function isPaymentReviewDataProvider()
    {
        return array(
            array(true, true, true),
            array(true, false, true),
            array(false, true, true),
            array(false, false, false)
        );
    }
}
