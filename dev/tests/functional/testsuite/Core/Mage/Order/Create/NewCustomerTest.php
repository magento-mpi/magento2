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
 * Creating order for new customer
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Order_Create_NewCustomerTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Log in to Backend and configure preconditions.</p>
     *
     * <p>Log in to Backend.</p>
     *
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return string
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');

        return $simple['general_sku'];
    }

    /**
     * <p>Create customer via 'Create order' without saving address</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId	TL-MAGE-3265
     */
    public function newCustomerWithoutAddress($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical',
            array('filter_sku' => $simpleSku, 'customer_email' => $this->generate('email', 32, 'valid')));
        $searchCustomer = $this->loadDataSet('Customers', 'search_customer',
            array('email' => $orderData['account_data']['customer_email']));
        $customerTitle = $orderData['billing_addr_data']['billing_first_name'] . ' '
                         . $orderData['billing_addr_data']['billing_last_name'];
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->navigate('manage_customers');
        $this->addParameter('elementTitle', $customerTitle);
        $this->customerHelper()->openCustomer($searchCustomer);
        $this->openTab('addresses');
        $addressCount = $this->getControlCount('pageelement', 'list_customer_address');
        $this->assertEquals(0, $addressCount, 'Customer should not have address, but have ' . $addressCount);
    }

    /**
     * <p>Create customer via 'Create order' with saving address</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3260
     */
    public function newCustomerWithAddress($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical',
            array('filter_sku' => $simpleSku, 'customer_email' => $this->generate('email', 32, 'valid')));
        $orderData['billing_addr_data'] = $this->loadDataSet('SalesOrder', 'billing_address_all');
        $orderData['shipping_addr_data'] = $this->loadDataSet('SalesOrder', 'shipping_address_all');
        $customerTitle = $orderData['billing_addr_data']['billing_first_name'] . ' '
                         . $orderData['billing_addr_data']['billing_last_name'];
        $searchCustomer = $this->loadDataSet('Customers', 'search_customer',
            array('email' => $orderData['account_data']['customer_email']));
        $addressVerify[] = $this->loadDataSet('SalesOrder', 'billing');
        $addressVerify[] = $this->loadDataSet('SalesOrder', 'shipping');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->navigate('manage_customers');
        $this->addParameter('elementTitle', $customerTitle);
        $this->customerHelper()->openCustomer($searchCustomer);
        $this->openTab('addresses');
        foreach ($addressVerify as $value) {
            $addressNumber = $this->customerHelper()->isAddressPresent($value);
            $this->assertNotEquals(0, $addressNumber, 'The specified address is not present.');
            $this->clearMessages('verification');
        }
    }

    /**
     * <p>Create customer via 'Create order' form (use exist email).</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3261
     */
    public function newCustomerWithExistEmail($simpleSku)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'customer_email' => $userData['email']));
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('error', 'customer_email_already_exists');
    }

    /**
     * <p>Create customer via 'Create order' form (use long email).</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3262
     */
    public function newCustomerWithLongEmail($simpleSku)
    {
        //Data
        $email = $this->generate('string', 129, ':alnum:') . '@unknown-domain.com';
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'customer_email' => $email));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('error', 'email_exceeds_allowed_length');
    }

    /**
     * <p>Create customer via 'Create order' form (not correct email).</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3263
     */
    public function newCustomerWithNotCorrectEmail($simpleSku)
    {
        //Data
        $email = $this->generate('string', 23, ':alnum:') . '@' . $this->generate('string', 65, ':alnum:') . '.org';
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'customer_email' => $email));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('error', 'email_is_not_valid_hostname');
        $this->assertMessagePresent('error', 'not_valid_hostname');
        $this->assertMessagePresent('error', 'hostname_not_valid');
    }

    /**
     * <p>Create customer via 'Create order' form</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3264
     */
    public function newCustomerWithNotValidEmail($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'customer_email' => $this->generate('email', 20, 'invalid')));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('validation', 'not_valid_email');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Create customer via 'Create order' form.</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3266
     */
    public function orderCompleteReqFields($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        $orderData['billing_addr_data'] = $this->orderHelper()->customerAddressGenerator(':alnum:', 'billing', 255);
        $orderData['shipping_addr_data'] = $this->orderHelper()->customerAddressGenerator(':alnum:', 'shipping', 255);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }
}