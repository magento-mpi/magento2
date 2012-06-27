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

/**
 * @magentoDataFixture Mage/Sales/_files/order.php
 */
class Mage_Paypal_PayflowadvancedControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function setUp()
    {
        parent::setUp();

        $order = new Mage_Sales_Model_Order();
        $order->load('100000001', 'increment_id');
        $order->getPayment()->setMethod(Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED);
        $order->save();

        $session = Mage::getSingleton('Mage_Checkout_Model_Session');
        $session->setLastRealOrderId($order->getRealOrderId())
            ->setLastQuoteId($order->getQuoteId());
    }

    public function testCancelPaymentActionIsContentGenerated()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('paypal/payflowadvanced/cancelpayment');
        $this->assertContains(
            'window_top.checkout.gotoSection("payment");',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window_top.document.getElementById(\'checkout-review-submit\').show();',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window_top.document.getElementById(\'iframe-warning\').hide();',
            $this->getResponse()->getBody()
        );
    }

    public function testReturnurlActionIsContentGenerated()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('paypal/payflowadvanced/returnurl');
        $this->assertContains(
            'window_top.checkout.gotoSection("payment");',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window_top.document.getElementById(\'checkout-review-submit\').show();',
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            'window_top.document.getElementById(\'iframe-warning\').hide();',
            $this->getResponse()->getBody()
        );
    }

    public function testFormActionIsContentGenerated()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('paypal/payflowadvanced/form');
        $this->assertContains(
            '<form id="token_form" method="POST" action="https://payflowlink.paypal.com/">',
            $this->getResponse()->getBody()
        );
    }
}
