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
     * @magentoConfigFixture frontend/design/theme/full_name magento_demo
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testSendEmail()
    {
        $order = Mage::getModel('\Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $shipment = Mage::getModel('\Magento\Sales\Model\Order\Shipment');
        $shipment->setOrder($order);

        $payment = $order->getPayment();
        $paymentInfoBlock = Mage::helper('Magento\Payment\Helper\Data')->getInfoBlock($payment);
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($shipment->getEmailSent());
        $shipment->sendEmail(true);
        $this->assertNotEmpty($shipment->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
