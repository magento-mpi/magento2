<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Controller;

/**
 * Test class for \Magento\Multishipping\Controller\Checkout
 *
 * @magentoAppArea frontend
 */
class CheckoutTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * Covers app/code/Magento/Checkout/Block/Multishipping/Payment/Info.php
     * and app/code/Magento/Checkout/Block/Multishipping/Overview.php
     *
     * @magentoDataFixture Magento/Sales/_files/quote.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture current_store shipping/option/checkout_multiple 1
     */
    public function testOverviewAction()
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Checkout\Model\Session')
            ->setQuoteId($quote->getId());
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        /** @var $session \Magento\Customer\Model\Session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Session', array($logger));
        $session->login('customer@example.com', 'password');
        $this->getRequest()->setPost('payment', array('method' => 'checkmo'));
        $this->dispatch('multishipping/checkout/overview');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<div class="box method">', $html);
        $this->assertContains('<dt class="title">'
            . $quote->getPayment()->getMethodInstance()->getTitle() . '</dt>', $html);
        $this->assertContains('<span class="price">$10.00</span>', $html);
    }
}
