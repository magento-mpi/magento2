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

class Magento_Sales_Model_Order_InvoiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture frontend/design/theme/full_name magento_demo
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testSendEmail()
    {
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $order = Mage::getModel('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $invoice = Mage::getModel('Magento_Sales_Model_Order_Invoice');
        $invoice->setOrder($order);

        $payment = $order->getPayment();
        $paymentInfoBlock = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Payment_Helper_Data')
            ->getInfoBlock($payment);
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($invoice->getEmailSent());
        $invoice->sendEmail(true);
        $this->assertNotEmpty($invoice->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
