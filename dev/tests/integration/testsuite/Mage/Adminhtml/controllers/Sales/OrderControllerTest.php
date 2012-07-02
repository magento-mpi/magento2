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

    /**
     * @magentoDataFixture Mage/Adminhtml/controllers/Sales/_files/address.php
     */
    public function testAddressActionNoVAT()
    {
        $address = new Mage_Sales_Model_Order_Address;
        $address->load('a_unique_firstname', 'firstname');
        $this->getRequest()->setParam('address_id', $address->getId());
        $this->dispatch('admin/sales_order/address');
        $html = $this->getResponse()->getBody();
        $prohibitedStrings = array('validate-vat', 'validateVat', 'Validate VAT');
        foreach ($prohibitedStrings as $string) {
            $this->assertNotContains($string, $html, 'VAT button must not be shown while editing address', true);
        }
    }
}
