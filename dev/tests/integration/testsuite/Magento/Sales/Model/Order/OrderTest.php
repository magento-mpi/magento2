<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testSendNewOrderEmail()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()
            ->loadArea(\Magento\Framework\App\Area::AREA_FRONTEND);
        $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $payment = $order->getPayment();
        $paymentInfoBlock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Payment\Helper\Data'
        )->getInfoBlock(
            $payment
        );
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($order->getEmailSent());
        $order->sendNewOrderEmail();
        $this->assertNotEmpty($order->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
