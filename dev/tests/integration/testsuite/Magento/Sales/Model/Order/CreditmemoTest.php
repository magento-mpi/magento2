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

namespace Magento\Sales\Model\Order;

class CreditmemoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testSendEmail()
    {
        \Mage::app()->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $order = \Mage::getModel('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');
        $order->setCustomerEmail('customer@example.com');

        $creditmemo = \Mage::getModel('Magento\Sales\Model\Order\Creditmemo');
        $creditmemo->setOrder($order);

        $payment = $order->getPayment();
        $paymentInfoBlock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Payment\Helper\Data')
            ->getInfoBlock($payment);
        $paymentInfoBlock->setArea('invalid-area');
        $payment->setBlockMock($paymentInfoBlock);

        $this->assertEmpty($creditmemo->getEmailSent());
        $creditmemo->sendEmail(true);
        $this->assertNotEmpty($creditmemo->getEmailSent());
        $this->assertEquals('frontend', $paymentInfoBlock->getArea());
    }
}
