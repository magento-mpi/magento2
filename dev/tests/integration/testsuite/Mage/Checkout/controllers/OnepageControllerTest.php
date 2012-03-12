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
    protected function _enableQuote()
    {
        $quote = new Mage_Sales_Model_Quote();
        $quote->load('test01', 'reserved_order_id');
        Mage::getSingleton('Mage_Checkout_Model_Session')->setQuoteId($quote->getId());
    }

    public function testProgressAction()
    {
        $this->_enableQuote();
        $this->dispatch('checkout/onepage/progress');
        $this->assertContains('Checkout', $this->getResponse()->getBody());
    }

    public function testShippingMethodAction()
    {
        $this->_enableQuote();
        $this->dispatch('checkout/onepage/shippingmethod');
        $this->assertContains('no quotes are available', $this->getResponse()->getBody());
    }

    public function testReviewAction()
    {
        $this->_enableQuote();
        $this->dispatch('checkout/onepage/review');
        $this->assertContains('checkout-review', $this->getResponse()->getBody());
    }
}
