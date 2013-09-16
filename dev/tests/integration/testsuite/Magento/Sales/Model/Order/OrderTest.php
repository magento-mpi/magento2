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

class Magento_Sales_Model_Order_OrderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testSendNewOrderEmail()
    {
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $order = Mage::getModel('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $payment = $order->getPayment();
        $paymentInfoBlock = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Payment_Helper_Data')
            ->getInfoBlock($payment);
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($order->getEmailSent());
        $order->sendNewOrderEmail();
        $this->assertNotEmpty($order->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
