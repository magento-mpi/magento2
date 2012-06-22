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

class Mage_Sales_Model_Order_InvoiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture sales_email/invoice/enabled 1
     * @magentoConfigFixture current_store design/theme/full_name default/default/default
     * @magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testSendEmail()
    {
        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $invoice = new Mage_Sales_Model_Order_Invoice();
        $invoice->setOrder($order);
        $paymentInfoBlock = Mage::helper('Mage_Payment_Helper_Data')->getInfoBlock($order->getPayment());
        $paymentInfoBlock->setArea('invalid-area');
        $invoice->setPaymentInfoBlock($paymentInfoBlock);

        $this->assertNull($invoice->getEmailSent());
        $invoice->sendEmail(true);
        $this->assertTrue($invoice->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
