<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Community2_Mage_Store_SingleStoreModeEditCustomerTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }

    /**
     * Create customer
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/enable_single_store_mode');
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //!!!!!!!!NEED ADD FUNCTION FOR DELETE ALL STORE VIEW!!!!!!!!!!!!
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        return $userData;
    }

    /**
     * <p>Create Customer Page</p>
     * <p>Magento contain only one store view</p>
     * <p>Enable single store mode System->Configuration->General->General->Single-Store Mode</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Go to Customer->Manege Customer</p>
     * <p>3. Click "Add new customer" button</p>
     * <p>4. Verify fields in account information tab</p>
     * <p>Expected Result</p>
     * <p>1. Dropdowns "Associate to Website" and "Send From" are missing</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6229
     * @author Maksym_Iakusha
     */
    public function newCustomer()
    {
        $this->admin('manage_customers');
        $this->clickButton('add_new_customer');
        //Validation
        $this->assertFalse($this->controlIsPresent('dropdown', 'associate_to_website'),
            "Dropdown associate_to_website present on page");
        $this->assertFalse($this->controlIsPresent('dropdown', 'send_from'), "Dropdown send_from present on page");
    }

    /**
     * <p>Edit Customer Page</p>
     * <p>Magento contain only one store view</p>
     * <p>Customer is created</p>
     * <p>Single store mode (System->Configuration->General->General->Single-Store Mode) is enabled</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Go to Customer->Manege Customer</p>
     * <p>3. Open customer profile</p>
     * <p>4. Verify that:</p>
     * <p>Sales statistic grid not contain "Website", "Store", "Store View" columns</p>
     * <p>Account information tab not contain "Associate to Website" dropdown</p>
     * <p>Table on Orders tab is not contain "Bought From" column</p>
     * <p>Table on Recurring Profile is not contain "Store" column</p>
     * <p>Table on Wishlist tab is not contain "Added From" column</p>
     * <p>Table on Product Review tab is not contain "Visible In" Column</p>
     * <p>Expected Result</p>
     * <p>1. All of the above elements are missing</p>
     *
     * @param $userData
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-6230
     * @author Maksym_Iakusha
     */
    public function editCustomer($userData)
    {
        //Data
        $param = $userData['first_name'] . ' ' . $userData['last_name'];
        $this->addParameter('customer_first_last_name', $param);
        //Preconditions
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $columnsName = $this->shoppingCartHelper()->getColumnNamesAndNumbers('sales_statistics_head');
        $this->assertTrue((!isset($columnsName['website']) && !isset($columnsName['store'])
                           && !isset($columnsName['store_view'])), "Sales Statistics table contain unnecessary column");
        $this->openTab('account_information');
        $this->assertFalse($this->controlIsPresent('dropdown', 'associate_to_website'),
            "Dropdown associate_to_website present on page");
        $this->openTab('orders');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_bought_from'),
            "Table contain 'bought_from' column");
        $this->openTab('recuring_profiles');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store'), "Table contain 'store' column");
        $this->openTab('wishlist');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_added_from'),
            "Table contain 'added_from' column");
        $this->openTab('product_review');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "Table contain 'visible_in' column");
    }
}
