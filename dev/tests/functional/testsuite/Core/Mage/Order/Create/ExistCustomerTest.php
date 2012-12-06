<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Order
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test with variations of address
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Order_Create_ExistCustomerTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Log in to Backend.</p>
     */
    public function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * @return string
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('Customers', 'all_fields_address');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('sku'   => $simple['general_name'],
                     'email' => $userData['email']);
    }

    /**
     * <p>Creating order for existing customer with same billing and shipping addresses.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and choose existing customer from the list;</p>
     * <p>3. Choose existing address for billing and shipping;</p>
     * <p>4. Fill in all required fields
     * (add products, add payment method information, choose shipping method, etc);</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear;</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId	TL-MAGE-3259
     */
    public function existingCustomerWithAddress($testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical',
                                        array('filter_sku' => $testData['sku'],
                                              'email'      => $testData['email']));
        unset($orderData['billing_addr_data']);
        unset($orderData['shipping_addr_data']);
        //Steps And Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOrder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }
}