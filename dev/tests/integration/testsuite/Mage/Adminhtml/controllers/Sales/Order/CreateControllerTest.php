<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Sales_Order_CreateControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testLoadBlockAction()
    {
        $this->getRequest()->setParam('block', ',');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('admin/sales_order_create/loadBlock');
        $this->assertEquals('{"message":""}', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testLoadBlockActionData()
    {
        Mage::getSingleton('Mage_Adminhtml_Model_Sales_Order_Create')->addProducts(array(1 => array('qty' => 1)));
        $this->getRequest()->setParam('block', 'data');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('admin/sales_order_create/loadBlock');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<div id=\"sales_order_create_search_grid\">', $html);
        $this->assertContains('<div id=\"order-billing_method_form\">', $html);
        $this->assertContains('id=\"shipping-method-overlay\"', $html);
        $this->assertContains('id=\"coupons:code\"', $html);
    }

    /**
     * @magentoConfigFixture admin_store payment/ccsave/centinel 1
     * @magentoConfigFixture admin_store payment_services/centinel/processor_id 134-01
     * @magentoConfigFixture admin_store payment_services/centinel/merchant_id magentoTEST
     * @magentoConfigFixture admin_store payment_services/centinel/password mag3nt0T3ST
     * @magentoConfigFixture admin_store payment_services/centinel/test_mode 1
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testIndexAction()
    {
        /** @var $order Mage_Adminhtml_Model_Sales_Order_Create */
        $order = Mage::getSingleton('Mage_Adminhtml_Model_Sales_Order_Create');
        $paymentData = array(
            'cc_owner' => 'Test User',
            'cc_type' => 'visa',
            'cc_number' => '4111111111111111',
            'cc_exp_month' => '12',
            'cc_exp_year' => '2013',
            'cc_cid' => '123',
            'method' => 'ccsave',
        );
        $order->addProducts(array(1 => array('qty' => 1)))->getQuote()->getPayment()->addData($paymentData);
        $this->dispatch('admin/sales_order_create/index');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<div id="order-customer-selector"', $html);
        $this->assertContains('<div id="sales_order_create_customer_grid">', $html);
        $this->assertContains('<div id="order-billing_method_form">', $html);
        $this->assertContains('id="shipping-method-overlay"', $html);
        $this->assertContains('<div id="sales_order_create_search_grid">', $html);
        $this->assertContains('id="coupons:code"', $html);
        $this->assertContains('<div class="centinel">', $html);
    }
}
