<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Sales_OrderControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testIndexAction()
    {
        $this->dispatch('admin/sales_order/index');
        $this->assertContains('Total 0 records found', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testIndexActionWithOrder()
    {
        $this->dispatch('admin/sales_order/index');
        $this->assertContains('Total 1 records found', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testOrderViewAction()
    {
        $order = new Mage_Sales_Model_Order;
        $order->load('100000001', 'increment_id');
        $this->dispatch('admin/sales_order/view/order_id/' . $order->getId());
        $this->assertContains('Los Angeles', $this->getResponse()->getBody());
    }
}
