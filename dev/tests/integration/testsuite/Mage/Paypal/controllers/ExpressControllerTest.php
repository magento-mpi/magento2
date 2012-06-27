<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Paypal_ExpressControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/Sales/_files/quote.php
     * @magentoDataFixture Mage/Paypal/_files/quote_payment.php
     */
    public function testReviewAction()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $quote = new Mage_Sales_Model_Quote();
        $quote->load('test01', 'reserved_order_id');
        Mage::getSingleton('Mage_Checkout_Model_Session')->setQuoteId($quote->getId());

        $this->dispatch('paypal/express/review');

        $html = $this->getResponse()->getBody();
        $this->assertContains('Simple Product', $html);
        $this->assertContains('Review', $html);
        $this->assertContains('/paypal/express/placeOrder/', $html);
    }
}
