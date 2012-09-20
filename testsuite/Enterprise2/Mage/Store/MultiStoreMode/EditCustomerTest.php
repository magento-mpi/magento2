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

class Community2_Mage_Store_MultiStoreMode_EditCustomerTest extends Mage_Selenium_TestCase
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
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');

        return $userData;
    }

    public function storeModeDataProvider()
    {
        return array(
            array ('enable_single_store_mode'),
            array ('disable_single_store_mode')
        );
    }

    /**
     * <p>Edit Customer Page</p>
     * <p>Preconditions</p>
     * <p>Magento contain only one store view</p>
     * <p>Customer is created</p>
     * <p>Single store mode (System->Configuration->General->General->Single-Store Mode) is enabled</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Go to Customer->Manege Customer</p>
     * <p>3. Open customer profile</p>
     * <p>4. Verify that:</p>
     * <p>Sales statistic grid not contain "Website", "Store", "Store View" columns</p>
     * <p>Account information tab contain "Associate to Website" dropdown</p>
     * <p>Table on Orders tab is contain "Bought From" column</p>
     * <p>Table on Recurring Profile is contain "Store" column</p>
     * <p>Table on Wishlist tab is contain "Added From" column</p>
     * <p>Table on Product Review tab is contain "Visible In" Column</p>
     * <p>Expected Result</p>
     * <p>1. All of the above elements are present</p>
     *
     * @param array $mode
     * @param array $userData
     *
     * @depends preconditionsForTests
     * @dataProvider storeModeDataProvider
     * @test
     * @TestlinkId TL-MAGE-6232
     * @author Maksym_Iakusha
     */
    public function editCustomer($mode, $userData)
    {
        //Data
        $param = $userData['first_name'] . ' ' . $userData['last_name'];
        $this->addParameter('customer_first_last_name', $param);
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$mode");
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $columnsName = $this->shoppingCartHelper()->getColumnNamesAndNumbers('sales_statistics_head');
        $this->assertTrue((isset($columnsName['website']) && isset($columnsName['store'])
                           && isset($columnsName['store_view'])),
            "Sales Statistics table not contain unnecessary column");
        $this->openTab('account_information');
        $this->assertTrue($this->controlIsPresent('dropdown', 'associate_to_website'),
            "Dropdown associate_to_website absent on page");
        $this->openTab('orders');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_bought_from'),
            "Table not contain 'bought_from' column");
        $this->openTab('recuring_profiles');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store'), "Table not contain 'store' column");
        $this->openTab('wishlist');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_added_from'),
            "Table not contain 'added_from' column");
        $this->openTab('product_review');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "Table not contain 'visible_in' column");
        $this->openTab('store_credit');
        $storeCredit2 = $this->shoppingCartHelper()->getColumnNamesAndNumbers('store_credit_balance_head');
        $this->assertTrue((isset($storeCredit2['website'])), "Sales Statistics table is not contain 'website' column");
        $this->assertTrue($this->controlIsPresent('dropdown', 'website'), "Dropdown 'website' is absent");
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'), "Dropdown 'filter_website' is absent");
        $this->openTab('gift_regystry');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'), "Dropdown 'filter_website' is absent");
        $this->openTab('reward_points');
        $rewardGrid2 = $this->shoppingCartHelper()->getColumnNamesAndNumbers('reward_points_balance_head');;
        $this->assertTrue((isset($rewardGrid2['website'])), "Sales Statistics table is not contain 'website' column");
        $this->assertTrue($this->controlIsPresent('dropdown', 'store'), "Dropdown 'store' is absent");
        $this->clickControl('link', 'reward_points_history_link', false);
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'), "Dropdown 'filter_website' is absent");
    }
}
