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
class Mage_Paypal_PayflowControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function setUp()
    {
        parent::setUp();

        $order = Mage::getModel('Mage_Sales_Model_Order');
        $order->load('100000001', 'increment_id');
        $order->getPayment()->setMethod(Mage_Paypal_Model_Config::METHOD_PAYFLOWLINK);
        $order->save();

        $session = Mage::getSingleton('Mage_Checkout_Model_Session');
        $session->setLastRealOrderId($order->getRealOrderId())
            ->setLastQuoteId($order->getQuoteId());
    }

    public function testCancelPaymentActionIsContentGenerated()
    {
        $this->dispatch('paypal/payflow/cancelpayment');
        $this->assertContains(
            "parent.jQuery('#checkoutSteps').trigger('gotoSection', 'payment');",
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            "parent.jQuery('#checkout-review-submit').show();",
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            "parent.jQuery('#iframe-warning').hide();",
            $this->getResponse()->getBody()
        );
    }

    public function testReturnurlActionIsContentGenerated()
    {
        $this->dispatch('paypal/payflow/returnurl');
        $this->assertContains(
            "parent.jQuery('#checkoutSteps').trigger('gotoSection', 'payment');",
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            "parent.jQuery('#checkout-review-submit').show();",
            $this->getResponse()->getBody()
        );
        $this->assertContains(
            "parent.jQuery('#iframe-warning').hide();",
            $this->getResponse()->getBody()
        );
    }

    public function testFormActionIsContentGenerated()
    {
        $this->dispatch('paypal/payflow/form');
        $this->assertContains(
            '<form id="token_form" method="POST" action="https://payflowlink.paypal.com/">',
            $this->getResponse()->getBody()
        );
    }
}
