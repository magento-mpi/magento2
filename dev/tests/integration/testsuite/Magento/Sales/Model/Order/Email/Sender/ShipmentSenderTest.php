<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Email\Sender;

class ShipmentSenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testSend()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\State')
            ->setAreaCode('frontend');
        $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $shipment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\Order\Shipment'
        );
        $shipment->setOrder($order);

        $payment = $order->getPayment();
        $paymentInfoBlock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Payment\Helper\Data'
        )->getInfoBlock(
            $payment
        );
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($shipment->getEmailSent());

        $orderSender = Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order\Email\Sender\ShipmentSender');
        $result = $orderSender->send($shipment, true);

        $this->assertTrue($result);

        $this->assertNotEmpty($shipment->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }

    /**
     * Check the correctness and stability of set/get packages of shipment
     *
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testPackages()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');
        $order = $objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $payment = $order->getPayment();
        $paymentInfoBlock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Payment\Helper\Data'
        )->getInfoBlock(
            $payment
        );
        $payment->setBlockMock($paymentInfoBlock);

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $objectManager->create('Magento\Sales\Model\Order\Shipment');
        $shipment->setOrder($order);

        $packages = array(array('1'), array('2'));

        $shipment->addItem($objectManager->create('Magento\Sales\Model\Order\Shipment\Item'));
        $shipment->setPackages($packages);
        $this->assertEquals($packages, $shipment->getPackages());
        $shipment->save();
        $shipment->save();
        $shipment->load($shipment->getId());
        $this->assertEquals($packages, $shipment->getPackages());
    }
}
