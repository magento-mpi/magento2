<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Block_Order_Print_InvoiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetInvoiceTotalsHtml()
    {
        $order = Mage::getModel('Magento\Sales\Model\Order');
        Mage::register('current_order', $order);
        $payment = Mage::getModel('Magento\Sales\Model\Order\Payment');
        $payment->setMethod('checkmo');
        $order->setPayment($payment);

        $layout = Mage::getModel('Magento\Core\Model\Layout');
        $block = $layout->createBlock('Magento\Sales\Block\Order\Print\Invoice', 'block');
        $childBlock = $layout->addBlock('\Magento\Core\Block\Text', 'invoice_totals', 'block');

        $expectedHtml = '<b>Any html</b>';
        $invoice = Mage::getModel('Magento\Sales\Model\Order\Invoice');
        $this->assertEmpty($childBlock->getInvoice());
        $this->assertNotEquals($expectedHtml, $block->getInvoiceTotalsHtml($invoice));

        $childBlock->setText($expectedHtml);
        $actualHtml = $block->getInvoiceTotalsHtml($invoice);
        $this->assertSame($invoice, $childBlock->getInvoice());
        $this->assertEquals($expectedHtml, $actualHtml);
    }
}
