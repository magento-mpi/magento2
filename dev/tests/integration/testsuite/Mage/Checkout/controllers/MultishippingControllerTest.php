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
 */
class Mage_Checkout_MultishippingControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    protected function setUp()
    {
        parent::setUp();
        $this->_quote = new Mage_Sales_Model_Quote();
        $this->_quote->load('test01', 'reserved_order_id');
        Mage::getSingleton('Mage_Checkout_Model_Session')->setQuoteId($this->_quote->getId());
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/quote.php
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testOverviewAction()
    {
        $session = new Mage_Customer_Model_Session;
        $session->login('customer@example.com', 'password');
        $this->getRequest()->setPost('payment', array('method' => 'checkmo'));
        $this->dispatch('checkout/multishipping/overview');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<p>' . $this->_quote->getPayment()->getMethodInstance()->getTitle() . '</p>', $html);
        $this->assertContains('<span class="price">$10.00</span>', $html);
    }
}
