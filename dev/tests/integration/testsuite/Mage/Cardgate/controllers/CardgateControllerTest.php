<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Cardgate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Sales/_files/order.php
 */
class Mage_Cardgate_CardgateControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function setUp()
    {
        parent::setUp();

        $order = Mage::getModel('Mage_Sales_Model_Order');
        $order->load('100000001', 'increment_id');
        $order->getPayment()->setMethod('cardgate_creditcard');

        $quote = Mage::getModel('Mage_Sales_Model_Quote')
            ->setStoreId($order->getStoreId())
            ->save();

        $order->setQuoteId($quote->getId());
        $order->save();

        $session = Mage::getSingleton('Mage_Checkout_Model_Session');
        $session->setLastRealOrderId($order->getRealOrderId())
            ->setLastQuoteId($order->getQuoteId());
    }

    public function testRedirectActionIsContentGenerated()
    {
        $this->dispatch('cardgate/cardgate/redirect/model/creditcard');
        $this->assertContains(
            '<form action="https://gateway.cardgateplus.com/" id="cardgateplus_checkout"'
            . ' name="cardgateplus_checkout" method="POST">',
            $this->getResponse()->getBody()
        );
    }

    public function testCancelActionIsContentGenerated()
    {
        $this->dispatch('cardgate/cardgate/cancel');
        $this->assertContains("checkout/cart", $this->getResponse()->getHeader('Location'));
    }

    public function testSuccessActionIsContentGenerated()
    {
        $this->dispatch('cardgate/cardgate/success');
        $this->assertContains("checkout/onepage/success", $this->getResponse()->getHeader('Location'));
    }
}
