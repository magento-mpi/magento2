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

class Magento_Sales_Model_Order_ShipmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testSendEmail()
    {
        $order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $shipment = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Shipment');
        $shipment->setOrder($order);

        $payment = $order->getPayment();
        $paymentInfoBlock = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Payment_Helper_Data')
            ->getInfoBlock($payment);
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($shipment->getEmailSent());
        $shipment->sendEmail(true);
        $this->assertNotEmpty($shipment->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
