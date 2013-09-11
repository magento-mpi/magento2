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
 * Test class for \Magento\Checkout\Controller\Multishipping
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
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = Mage::getModel('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        Mage::getSingleton('Magento\Checkout\Model\Session')->setQuoteId($quote->getId());
        /** @var $session \Magento\Customer\Model\Session */
        $session = Mage::getModel('Magento\Customer\Model\Session');
        $session->login('customer@example.com', 'password');
        $this->getRequest()->setPost('payment', array('method' => 'checkmo'));
        $this->dispatch('checkout/multishipping/overview');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<p>' . $quote->getPayment()->getMethodInstance()->getTitle() . '</p>', $html);
        $this->assertContains('<span class="price">$10.00</span>', $html);
    }
}
