<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search product/customer/order via global search in backend
 */
class Core_Mage_AdminGlobalSearch_SearchTest extends Mage_Selenium_TestCase
{

    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * Create new product, customer and order.
     *
     * @return array
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $product = $this->loadDataSet('Product', 'virtual_product_required');
        $orderData = $this->loadDataSet('SalesOrder', 'order_virtual',
            array('filter_sku' => $product['general_sku'], 'customer_email' => $this->generate('email', 32, 'valid')));
        //Create new product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Create new order with new customer
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');

        return array(
            'productSku' => $product['general_sku'],
            'productName' => $product['general_name'],
            'customerFirstName' => $orderData['billing_addr_data']['billing_first_name'],
            'customerLastName' => $orderData['billing_addr_data']['billing_last_name'],
            'orderId' => $orderId
        );
    }

    /**
     * Search product via sku
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkid TL-MAGETWO-17
     */
    public function searchProduct($data)
    {
        //Steps
        $this->addParameter('elementTitle', $data['productName']);
        $this->adminGlobalSearchHelper()->searchAndOpen($data['productSku'], $data['productName']);
        $this->assertEquals('edit_product', $this->getCurrentPage(), 'Wrong page was opened');
    }

    /**
     * Search customer via last name
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkid TL-MAGETWO-18
     */
    public function searchCustomer($data)
    {
        //Data
        $customerName = $data['customerFirstName'] . ' ' . $data['customerLastName'];
        //Steps
        $this->addParameter('elementTitle', $customerName);
        $this->adminGlobalSearchHelper()->searchAndOpen($data['customerLastName'], $customerName);
        $this->assertEquals('edit_customer', $this->getCurrentPage(), 'Wrong page was opened');
    }

    /**
     * Search order via order id
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkid TL-MAGETWO-19
     */
    public function searchOrder($data)
    {
        //Steps
        $this->addParameter('elementTitle', '#' . $data['orderId']);
        $this->adminGlobalSearchHelper()->searchAndOpen($data['orderId'], 'Order #' . $data['orderId']);
        $this->assertEquals('view_order', $this->getCurrentPage(), 'Wrong page was opened');
    }

    /**
     * Search invalid value
     *
     * @test
     * @TestLinkid TL-MAGETWO-20
     */
    public function searchNoRecords()
    {
        //Steps
        $this->clickButton('global_search');
        $this->waitForControlEditable(self::FIELD_TYPE_INPUT, 'global_record_search');
        $this->getControlElement(self::FIELD_TYPE_INPUT, 'global_record_search')->value($this->generate('string', 15));
        $this->pleaseWait();
        $this->waitForControlVisible(self::FIELD_TYPE_PAGEELEMENT, 'search_global_list');
        $this->assertTrue($this->controlIsVisible(self::FIELD_TYPE_PAGEELEMENT, 'global_record_no_records'),
            'Some records were found');
    }
}
