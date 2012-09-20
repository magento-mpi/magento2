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

class Enterprise2_Mage_Store_DisableSingleStoreMode_EditCustomerTest extends Mage_Selenium_TestCase
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
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/enable_single_store_mode');

        return $userData;
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
     * <p>ONLY FOR EE</p>
     * <p> Reward Points grid not contain "website" column</p>
     * <p> Store Credit grid not contain 'Website' column</p>
     * <p> Store credit tab not contain "website" drtopdown in 'Update balance' fieldset and filter "Website" in Balance History</p>
     * <p>Gift registry tab not contain 'Website' filter</p>
     * <p>Reward Points tab not contain "website" drtopdown in 'Update balance' fieldset and filter "Website" in Balance History</p>
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
        $rewardGrid = $this->shoppingCartHelper()->getColumnNamesAndNumbers('reward_points_head');
        $this->assertTrue((!isset($rewardGrid['website'])), "Sales Statistics table contain 'Website' column");
        $storeCreditGrid = $this->shoppingCartHelper()->getColumnNamesAndNumbers('store_credit_head');
        $this->assertTrue((!isset($storeCreditGrid['website'])), "Sales Statistics table contain 'website' column");
        $salesGrid = $this->shoppingCartHelper()->getColumnNamesAndNumbers('sales_statistics_head');
        $this->assertTrue((!isset($salesGrid['website']) && !isset($salesGrid['store'])
                           && !isset($salesGrid['store_view'])), "Sales Statistics table contain unnecessary columns");
        $this->openTab('account_information');
        $this->assertFalse($this->controlIsPresent('dropdown', 'associate_to_website'),
            "Dropdown associate_to_website is present on page");
        $this->openTab('orders');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_bought_from'),
            "Table is contain 'bought_from' column");
        $this->openTab('recuring_profiles');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store'), "Table is contain 'store' column");
        $this->openTab('wishlist');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_added_from'),
            "Table is contain 'added_from' column");
        $this->openTab('product_review');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "Table is contain 'visible_in' column");
        $this->openTab('store_credit');
        $storeCredit2 = $this->shoppingCartHelper()->getColumnNamesAndNumbers('store_credit_balance_head');
        $this->assertTrue((!isset($storeCredit2['website'])), "Sales Statistics table is contain 'website' column");
        $this->assertFalse($this->controlIsPresent('dropdown', 'website'), "Dropdown 'website' is present");
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            "Dropdown 'filter_website' is present");
        $this->openTab('gift_regystry');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            "Dropdown 'filter_website' is present");
        $this->openTab('reward_points');
        $rewardGrid2 = $this->shoppingCartHelper()->getColumnNamesAndNumbers('reward_points_balance_head');
        ;
        $this->assertTrue((!isset($rewardGrid2['website'])), "Sales Statistics table is contain 'website' column");
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'), "Dropdown 'store' is present");
        $this->clickControl('link', 'reward_points_history_link', false);
        $this->waitForAjax();
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            "Dropdown 'filter_website' is present");
    }
}
