<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller;

class StandardTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    protected function setUp()
    {
        parent::setUp();
        $this->_order = $this->_objectManager->create('Magento\Sales\Model\Order');
        $this->_session = $this->_objectManager->get('Magento\Checkout\Model\Session');
    }

    /**
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_standard.php
     * @magentoConfigFixture current_store payment/paypal_standard/active 1
     * @magentoConfigFixture current_store paypal/general/business_account merchant_2012050718_biz@example.com
     */
    public function testCancelAction()
    {
        $quote = $this->_objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        $this->_session->setQuoteId($quote->getId());
        $this->_session->setPaypalStandardQuoteId($quote->getId())
            ->setLastRealOrderId('100000002');
        $this->dispatch('paypal/standard/cancel');

        $this->_order->load('100000002', 'increment_id');
        $this->assertEquals('canceled', $this->_order->getState());
        $this->assertEquals($this->_session->getQuote()->getGrandTotal(), $quote->getGrandTotal());
        $this->assertEquals($this->_session->getQuote()->getItemsCount(), $quote->getItemsCount());
    }
}
