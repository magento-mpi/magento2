<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Sales_Order_Invoice_View
 */
class Mage_Adminhtml_Block_Sales_Order_Invoice_ViewTest extends PHPUnit_Framework_TestCase
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
        $order = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->getMock();
        $order->expects($this->any())
            ->method('canReviewPayment')
            ->will($this->returnValue($canReviewPayment));
        $order->expects($this->any())
            ->method('canFetchPaymentReviewUpdate')
            ->will($this->returnValue($canFetchUpdate));

        // Create invoice mock
        $invoice = $this->getMockBuilder('Mage_Sales_Model_Order_Invoice')
            ->disableOriginalConstructor()
            ->setMethods(array('getOrder'))
            ->getMock();
        $invoice->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($order));

        // Prepare block to test protected method
        $block = $this->getMockBuilder('Mage_Adminhtml_Block_Sales_Order_Invoice_View')
            ->disableOriginalConstructor()
            ->setMethods(array('getInvoice'))
            ->getMock();
        $block->expects($this->once())
            ->method('getInvoice')
            ->will($this->returnValue($invoice));
        $testMethod = new ReflectionMethod('Mage_Adminhtml_Block_Sales_Order_Invoice_View', '_isPaymentReview');
        $testMethod->setAccessible(true);

        $this->assertEquals($expectedResult, $testMethod->invoke($block));
    }

    public function isPaymentReviewDataProvider()
    {
        return array(
            array(true, true, true),
            array(true, false, true),
            array(false, true, true),
            array(false, false, false),
        );
    }
}
