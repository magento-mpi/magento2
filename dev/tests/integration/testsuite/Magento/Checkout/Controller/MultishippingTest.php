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

/**
 * Test class for Magento_Checkout_Controller_Multishipping
 *
 * @magentoAppArea frontend
 */
class Magento_Checkout_Controller_MultishippingTest extends Magento_TestFramework_TestCase_ControllerAbstract
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
        /** @var $quote Magento_Sales_Model_Quote */
        $quote = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Quote');
        $quote->load('test01', 'reserved_order_id');
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Checkout_Model_Session')
            ->setQuoteId($quote->getId());
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        /** @var $session Magento_Customer_Model_Session */
        $session = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Customer_Model_Session', array($logger));
        $session->login('customer@example.com', 'password');
        $this->getRequest()->setPost('payment', array('method' => 'checkmo'));
        $this->dispatch('checkout/multishipping/overview');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<p>' . $quote->getPayment()->getMethodInstance()->getTitle() . '</p>', $html);
        $this->assertContains('<span class="price">$10.00</span>', $html);
    }
}
