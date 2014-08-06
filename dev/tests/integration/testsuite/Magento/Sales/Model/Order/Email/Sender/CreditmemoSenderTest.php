<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Email\Sender;

class CreditmemoSenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testSend()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()
            ->loadArea(\Magento\Framework\App\Area::AREA_FRONTEND);
        $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $creditmemo = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\Order\Creditmemo'
        );
        $creditmemo->setOrder($order);

        $payment = $order->getPayment();
        $paymentInfoBlock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Payment\Helper\Data'
        )->getInfoBlock(
                $payment
            );
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($creditmemo->getEmailSent());

        $creditmemoSender = Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order\Email\Sender\CreditmemoSender');
        $result = $creditmemoSender->send($creditmemo, true);

        $this->assertTrue($result);
        $this->assertNotEmpty($creditmemo->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
