<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Paypal_Controller_ExpressTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/Sales/_files/quote.php
     * @magentoDataFixture Magento/Paypal/_files/quote_payment.php
     */
    public function testReviewAction()
    {
        $quote = Mage::getModel('Mage_Sales_Model_Quote');
        $quote->load('test01', 'reserved_order_id');
        Mage::getSingleton('Magento_Checkout_Model_Session')->setQuoteId($quote->getId());

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
        $quote = $this->_objectManager->create('Mage_Sales_Model_Quote');
        $quote->load('test02', 'reserved_order_id');
        $order = $this->_objectManager->create('Mage_Sales_Model_Order');
        $order->load('100000002', 'increment_id');
        $session = $this->_objectManager->get('Magento_Checkout_Model_Session');
        $session->setLastRealOrderId($order->getRealOrderId())
            ->setLastOrderId($order->getId())
            ->setLastQuoteId($order->getQuoteId())
            ->setQuoteId($order->getQuoteId());
        $paypalSession = $this->_objectManager->get('Magento_Paypal_Model_Session');
        $paypalSession->setExpressCheckoutToken('token');

        $this->dispatch('paypal/express/cancel');

        $order->load('100000002', 'increment_id');
        $this->assertEquals('canceled', $order->getState());
        $this->assertEquals($session->getQuote()->getGrandTotal(), $quote->getGrandTotal());
        $this->assertEquals($session->getQuote()->getItemsCount(), $quote->getItemsCount());
    }
}
