<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Sales/_files/quote.php
 */
class Mage_Checkout_OnepageControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected function setUp()
    {
        parent::setUp();
        $quote = new Mage_Sales_Model_Quote();
        $quote->load('test01', 'reserved_order_id');
        Mage::getSingleton('Mage_Checkout_Model_Session')->setQuoteId($quote->getId());
    }

    public function testIndexAction()
    {
        $this->dispatch('checkout/onepage/index');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<li id="opc-payment"', $html);
        $this->assertContains('<dl class="sp-methods" id="checkout-payment-method-load">', $html);
        $this->assertContains('<form id="co-billing-form" action="">', $html);
    }

    public function testProgressAction()
    {
        $steps = array(
            'payment' => array('is_show' => true, 'complete' => true),
            'billing' => array('is_show' => true),
            'shipping' => array('is_show' => true),
            'shipping_method' => array('is_show' => true),
        );
        Mage::getSingleton('Mage_Checkout_Model_Session')->setSteps($steps);

        $this->dispatch('checkout/onepage/progress');
        $html = $this->getResponse()->getBody();
        $this->assertContains('Checkout', $html);
        $methodTitle = Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote()->getPayment()->getMethodInstance()
            ->getTitle();
        $this->assertContains('<p>' . $methodTitle . '</p>', $html);
    }

    public function testShippingMethodAction()
    {
        $this->dispatch('checkout/onepage/shippingmethod');
        $this->assertContains('no quotes are available', $this->getResponse()->getBody());
    }

    public function testReviewAction()
    {
        $this->dispatch('checkout/onepage/review');
        $this->assertContains('checkout-review', $this->getResponse()->getBody());
    }
}
