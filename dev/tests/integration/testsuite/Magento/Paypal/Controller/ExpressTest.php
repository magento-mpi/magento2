<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Controller;

class ExpressTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Magento/Sales/_files/quote.php
     * @magentoDataFixture Magento/Paypal/_files/quote_payment.php
     */
    public function testReviewAction()
    {
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Checkout\Model\Session')
            ->setQuoteId($quote->getId());

        $this->dispatch('paypal/express/review');

        $html = $this->getResponse()->getBody();
        $this->assertContains('Simple Product', $html);
        $this->assertContains('Review', $html);
        $this->assertContains('/paypal/express/placeOrder/', $html);
    }

    /**
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_express.php
     * @magentoConfigFixture current_store paypal/general/business_account merchant_2012050718_biz@example.com
     */
    public function testCancelAction()
    {
        $quote = $this->_objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test02', 'reserved_order_id');
        $order = $this->_objectManager->create('Magento\Sales\Model\Order');
        $order->load('100000002', 'increment_id');
        $session = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $session->setLastRealOrderId($order->getRealOrderId())
            ->setLastOrderId($order->getId())
            ->setLastQuoteId($order->getQuoteId())
            ->setQuoteId($order->getQuoteId());
        /** @var $paypalSession \Magento\Session\Generic */
        $paypalSession = $this->_objectManager->get('Magento\Session\Generic');
        $paypalSession->setExpressCheckoutToken('token');

        $this->dispatch('paypal/express/cancel');

        $order->load('100000002', 'increment_id');
        $this->assertEquals('canceled', $order->getState());
        $this->assertEquals($session->getQuote()->getGrandTotal(), $quote->getGrandTotal());
        $this->assertEquals($session->getQuote()->getItemsCount(), $quote->getItemsCount());
    }
}
