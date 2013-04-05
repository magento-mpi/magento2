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
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend and configure preconditions.</p>
     *
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
    }

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

        return array('sku'   => $simple['general_sku'],
                     'email' => $userData['email']);
    }

    /**
     * <p>Creating order for existing customer with same billing and shipping addresses.</p>
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