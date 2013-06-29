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
 * Test class for Mage_Checkout_MultishippingController
 *
 * @magentoAppArea frontend
 */
class Mage_Checkout_MultishippingControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Covers app/code/Mage/Checkout/Block/Multishipping/Payment/Info.php
     * and app/code/Mage/Checkout/Block/Multishipping/Overview.php
     *
     * @magentoDataFixture Mage/Sales/_files/quote.php
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @magentoConfigFixture current_store shipping/option/checkout_multiple 1
     */
    public function testOverviewAction()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('Mage_Sales_Model_Quote');
        $quote->load('test01', 'reserved_order_id');
        Mage::getSingleton('Mage_Checkout_Model_Session')->setQuoteId($quote->getId());
        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getModel('Mage_Customer_Model_Session');
        $session->login('customer@example.com', 'password');
        $this->getRequest()->setPost('payment', array('method' => 'checkmo'));
        $this->dispatch('checkout/multishipping/overview');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<p>' . $quote->getPayment()->getMethodInstance()->getTitle() . '</p>', $html);
        $this->assertContains('<span class="price">$10.00</span>', $html);
    }
}
