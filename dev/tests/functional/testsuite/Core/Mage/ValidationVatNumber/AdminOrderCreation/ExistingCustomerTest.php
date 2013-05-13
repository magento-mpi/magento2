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
        $this->markTestIncomplete(
            'BUG: An error occurred while saving the customer in creatingOrderForExistingCustomer'
        );
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->systemConfigurationHelper()->configure('VatID/store_information_data');
        $this->clickControl('button', 'validate_vat_number', false);
        $this->pleaseWait();
        //Verification
        $this->assertTrue($this->controlIsVisible('pageelement', 'vat_number_is_valid'), 'VAT Number is not valid');
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
        $this->systemConfigurationHelper()->configure('ShippingSettings/store_information_empty');
    }

    /**
     * @return string
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $names = array(
            'group_valid_vat_intraunion' => 'Valid VAT Domestic_%randomize%',
            'group_valid_vat_domestic' => 'Valid VAT IntraUnion_%randomize%',
            'group_invalid_vat' => 'Invalid VAT_%randomize%',
            'group_default' => 'Default Group_%randomize%'
        );
        $groupNames = array();
        //Creating three Customer Groups
        $this->navigate('manage_customer_groups');
        foreach ($names as $groupKey => $groupName) {
            $group = $this->loadDataSet('CustomerGroup', 'new_customer_group', array('group_name' => $groupName));
            $this->customerGroupsHelper()->createCustomerGroup($group);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_customer_group');
            $groupNames[$groupKey] = $group['group_name'];
        }
        //Configuring "Create New Account Options" tab
        $this->navigate('system_configuration');
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options', $groupNames);
        $this->systemConfigurationHelper()->configure($accountOptions);
        //Data for creating product
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->flushCache();

        return array('sku' => $simple['general_sku'], 'groups' => $groupNames);
    }

    /**
     * <p>Creating order from back-end with different VAT Numbers for existing customers.</p>
     *
     * @param string $address
     * @param string $group
     * @param string $successChange
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @dataProvider creatingOrderForExistingCustomerDataProvider
     *
     * @TestlinkId TL-MAGE-4932, TL-MAGE-4956, TL-MAGE-4958
     */
    public function creatingOrderForExistingCustomer($address, $group, $successChange, $testData)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('group' => $testData['groups']['group_default']));
        $userAddressData = $this->loadDataSet('Customers', 'generic_address_' . $address);
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical', array(
            'filter_sku' => $testData['sku'],
            'email' => $userData['email'],
            'customer_group' => $testData['groups']['group_default'],
            'billing_addr_data' => '%noValue%',
            'shipping_addr_data' => '%noValue%'
        ));
        //Creating new customer
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $userAddressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->doAdminCheckoutSteps($orderData);
        $this->orderHelper()->validateVatNumber(
            $testData['groups']['group_default'],
            $testData['groups'][$group],
            $orderData['billing_addr_data']['billing_vat_number'],
            $successChange
        );
        $this->pleaseWait();
        $this->orderHelper()->submitOrder();
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
    }

    public function creatingOrderForExistingCustomerDataProvider()
    {
        return array(
            array('vat_valid_intraunion', 'group_valid_vat_intraunion', true),
            array('vat_valid_domestic', 'group_valid_vat_domestic', true),
            array('vat_invalid', 'group_invalid_vat', false)
        );
    }
}