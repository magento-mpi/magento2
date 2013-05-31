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
class Mage_Paypal_HostedproControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testCancelActionIsContentGenerated()
    {
        $order = Mage::getModel('Mage_Sales_Model_Order');
        $order->load('100000001', 'increment_id');
        $order->getPayment()->setMethod(Mage_Paypal_Model_Config::METHOD_HOSTEDPRO);

        $quote = Mage::getModel('Mage_Sales_Model_Quote')
            ->setStoreId($order->getStoreId())
            ->save();

        $order->setQuoteId($quote->getId());
        $order->save();

        $session = Mage::getSingleton('Mage_Checkout_Model_Session');
        $session->setLastRealOrderId($order->getRealOrderId())
            ->setLastQuoteId($order->getQuoteId());

        $this->dispatch('paypal/hostedpro/cancel');
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
}
