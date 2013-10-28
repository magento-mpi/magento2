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
 * @magentoDataFixture Magento/Sales/_files/quote.php
 */
class OnepageTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected function setUp()
    {
        parent::setUp();
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Checkout\Model\Session')
            ->setQuoteId($quote->getId());
    }

    /**
     * Covers onepage payment.phtml templates
     */
    public function testIndexAction()
    {
        $this->dispatch('checkout/onepage/index');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<li id="opc-payment"', $html);
        $this->assertContains('<dl class="sp-methods" id="checkout-payment-method-load">', $html);
        $this->assertSelectCount('form[id="co-billing-form"][action=""]', 1, $html);
    }

    public function testShippingMethodAction()
    {
        $this->dispatch('checkout/onepage/shippingmethod');
        $this->assertContains('no quotes are available', $this->getResponse()->getBody());
    }

    public function testReviewAction()
    {
        $this->dispatch('checkout/onepage/review');
        $this->assertContains('Place Order', $this->getResponse()->getBody());
        $this->assertContains('checkout-review', $this->getResponse()->getBody());
    }
}
