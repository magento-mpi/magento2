<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ValidationVatNumber
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ValidationVatNumber_AdminOrderCreation_ExistingCustomerTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options',
            array('default_value_for_disable_automatic_group' => 'Yes'));
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('VatID/store_information_data');
        $this->systemConfigurationHelper()->expandFieldSet('store_information');
        $this->clickControl('button', 'validate_vat_number', false);
        $this->pleaseWait();
        //Verification
        $this->assertTrue($this->controlIsPresent('button', 'vat_number_is_valid'), 'VAT Number is not valid');
        //Steps
        $this->navigate('system_configuration');
        //Verification
        $this->systemConfigurationHelper()->configure($accountOptions);
    }

    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('VatID/create_new_account_options_disable');
    }

    /**
     * @return string
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $names = array('group_valid_vat_domestic'   => 'Valid VAT Domestic_%randomize%',
                       'group_valid_vat_intraunion' => 'Valid VAT IntraUnion_%randomize%',
                       'group_invalid_vat'          => 'Invalid VAT_%randomize%',
                       'group_default'              => 'Default Group_%randomize%');
        $processedGroupNames = array();
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options', $processedGroupNames);
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Creating three Customer Groups
        $this->navigate('manage_customer_groups');
        foreach ($names as $groupKey => $groupName) {
            $group = $this->loadDataSet('CustomerGroup', 'new_customer_group', array('group_name' => $groupName));
            $this->customerGroupsHelper()->createCustomerGroup($group);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_customer_group');
            $processedGroupNames[$groupKey] = $group['group_name'];
        }
        //Configuring "Create New Account Options" tab
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($accountOptions);
        //Data for creating product
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('sku' => $simple['general_name'], 'customerGroups' => $processedGroupNames);
    }

    /**
     * <p>Creating order from back-end with different VAT Numbers for existing customers.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page</p>
     * <p>2. Create new order and choose existing customer from the list</p>
     * <p>3. Choose existing address for billing and shipping</p>
     * <p>4. Fill in all required fields</p>
     * (add products, add payment method information, choose shipping method, etc)</p>
     * <p>5. Click button "Validate VAT Number" and confirm popup</p>
     * <p>6. Customer group should be automatically changed, corresponding to settings</p>
     * <p>7. Click "Save" button</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear</p>
     *
     * @param array $customerAddressData
     * @param string $messageType
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @dataProvider creatingOrderForExistingCustomerDataProvider
     *
     * @TestlinkId TL-MAGE-4932, TL-MAGE-4956, TL-MAGE-4958
     * @author andrey.vergeles
     */
    public function creatingOrderForExistingCustomer($customerAddressData, $messageType, $testData)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('group' => $testData['customerGroups']['group_default']));
        $userAddressData = $this->loadDataSet('Customers', 'generic_address_' . $customerAddressData);
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical',
            array('filter_sku'     => $testData['sku'], 'email' => $userData['email'],
                  'customer_group' => '%noValue%'));
        unset($orderData['billing_addr_data']);
        unset($orderData['shipping_addr_data']);
        //Creating new customer
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $userAddressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_sales_orders');
        //Verification
        $this->orderHelper()->doAdminCheckoutSteps($orderData);
        $this->validationVatNumberHelper()->validationVatMessages($testData, $userAddressData, $messageType);
        $this->orderHelper()->submitOrder();
        $this->assertMessagePresent('success', 'success_created_order');
    }

    public function creatingOrderForExistingCustomerDataProvider()
    {
        return array(
            array('vat_valid_intraunion', 'validIntraunionMessage'),
            array('vat_valid_domestic', 'validDomesticMessage'),
            array('vat_invalid', 'invalidMessage')
        );
    }
}