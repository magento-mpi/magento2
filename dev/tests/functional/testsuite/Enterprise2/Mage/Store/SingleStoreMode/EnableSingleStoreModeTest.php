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

class Enterprise2_Mage_Store_SingleStoreMode_EnableSingleStoreModeTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>Precondition for test:</p>
     * <p>1. Login to backend.</p>
     * <p>2. Navigate to System -> Manage Store.</p>
     * <p>3. Verify that one store-view is created.</p>
     * <p>4. Go to System - Configuration - General and enable Single-Store Mode.</p>
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $config = $this->loadDataSet('SingleStoreMode', 'enable_single_store_mode');
        //Steps
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);

        return $userData;
    }

    /**
     * <p>Choose Scope selector does not displayed on the Manage Page Hierarchy page</p>
     * <p>Steps:</p>
     * <p>Navigate to Manage Pages Hierarchy page.</p>
     * <p>Expected result:</p>
     * <p>There is no "Choose Scope" selector  on the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6166
     * @author Nataliya_Kolenko
     */
    public function verificationManageHierarchy()
    {
        $this->navigate('manage_pages_hierarchy');
        $this->assertFalse($this->controlIsPresent('dropdown', 'choose_scope'),
            'There is "Choose Scope" selector on the page');
    }

    /**
     * <p>Assign to Store Views selector does not displayed in the New Widget Instance page</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Widget Instances page</p>
     * <p>2. Click "Add New Widget Instance" button</p>
     * <p>3. Fill Settings fields</p>
     * <p>4. Click "Continue"</p>
     * <p>Expected result:</p>
     * <p>There is no "Assign to Store Views" selector in the Frontend Properties tab</p>
     *
     * @param string $dataWidgetType
     *
     * @dataProvider widgetTypesDataProvider
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6169
     * @author Nataliya_Kolenko
     */
    public function verificationAllTypesOfWidgetsInSingleStoreMode($dataWidgetType)
    {
        $widgetData = $this->loadDataSet('CmsWidget', $dataWidgetType . '_settings');
        $this->navigate('manage_cms_widgets');
        $this->clickButton('add_new_widget_instance');
        $this->cmsWidgetsHelper()->fillWidgetSettings($widgetData['settings']);
        $this->assertFalse($this->controlIsPresent('multiselect', 'assign_to_store_views'),
            'There is "Store View" selector on the page');
    }

    public function widgetTypesDataProvider()
    {
        return array(
            array('banner_rotator'),
            array('cms_hierarchy_node_link'),
            array('catalog_events_carousel'),
            array('gift_registry_search'),
            array('order_by_sku'),
            array('wishlist_search'),
        );
    }

    /**
     * <p>All references to Website-Store-Store View do not displayed in the Banner area</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Banners page</p>
     * <p>2. Click "Add Banner" button</p>
     * <p>3. Choose Content tab</p>
     * <p>4. Click "Back" button</p>
     * <p>Expected result:</p>
     * <p>There is no "Store View Specific Content" fieldset in the Content tab</p>
     * <p>There is no "Visible In" column on the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6170
     * @author Nataliya_Kolenko
     */
    public function verificationBanners()
    {
        $this->navigate('manage_cms_banners');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_banner'),
            'There is no "Add Banner" button on the page');
        $this->clickButton('add_new_banner');
        $this->assertTrue($this->controlIsPresent('tab', 'content'), 'There is Content tab on the page');
        $this->openTab('content');
        $this->assertFalse($this->controlIsPresent('fieldset', 'specific_content'),
            'There is "Store View Specific Content" selector on the page');
        $this->clickButton('back');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            'There is "Visible In" dropdown on the page');
    }

    /**
     * <p>All references to Website-Store-Store View do not displayed in the Customer Segments area</p>
     * <p>Steps:<p/>
     * <p>1. Navigate to Manage Segments page</p>
     * <p>2. Click "Add Segment" button</p>
     * <p>Expected result:</p>
     * <p>There is no "Website" column on the page</p>
     * <p>There is no "Assigned to Website" multiselect on the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6226
     * @author Nataliya_Kolenko
     */
    public function verificationCustomerSegments()
    {
        $this->navigate('manage_customer_segments');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            'There is "Website" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_segment'),
            'There is no "Add Segment" button on the page');
        $this->clickButton('add_segment');
        $this->assertFalse($this->controlIsPresent('multiselect', 'assigned_to_website'),
            'There is "Assigned to Website" selector on the page');
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
     *
     * @test
     * @depends preconditionsForTests
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

    /**
     * <p>All references to Website-Store-Store View do not displayed in the Gift Card Accounts area</p>
     * <p>Steps:<p/>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p>
     * <p>6. Navigate to Manage Gift Card Accounts page.</p>
     * <p>7. Click "Add New Gift Card Account" button.</p>
     * <p>6. Navigate to Send Gift Cart tab.</p>
     * <p>Expected result:</p>
     * <p>There is no "Website" column on the page.</p>
     * <p>There is no "Website" multi selector on the page.</p>
     * <p>There is no "Send Email from the Following Store View" multi selector in the tab.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6238
     * @author Nataliya_Kolenko
     */
    public function verificationGiftCardAccount()
    {
        $this->navigate('manage_gift_card_account');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            'There is "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_gift_card_account'),
            'There is no "Add Gift Card Account" button on the page');
        $this->clickButton('add_gift_card_account');
        $this->assertFalse($this->controlIsPresent('multiselect', 'website'),
            'There is "Website" multi selector on the page');
        $this->assertTrue($this->controlIsPresent('tab', 'send_gift_card'),
            'There is no Send Gift Card tab on the page');
        $this->openTab('send_gift_card');
        $this->assertFalse($this->controlIsPresent('multiselect', 'send_email_from'),
            'There is Send Email from the Following Store View multi selector on the page');
    }

    /**
     * <p>Catalog Price Rules page does not contain websites columns and multiselects if Single Store Mode is enabled.</p>
     * <p>Steps:</p>
     * <p>1.Go to Promotions - Catalog Price Rules
     * <p>2.Check for Website column on the Grid.
     * <p>Expected result: </p>
     * <p>Website column is not displayed.</p>
     * <p>3.Click on the Add New Rule button.</p>
     * <p>4.Check for Websites multiselect</p>
     * <p>Expected result: </p>
     * <p>Websites multiselect is not displayed.</p>
     *  <p>5.Click on the Related Banners tab and check "Visible In" column</p>
     * <p>Expected result: </p>
     * <p>"Visible In" column is not displayed</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6266
     * @author Tatyana_Gonchar
     */
    public function verificationCatalogPriceRule()
    {
        $this->admin('manage_catalog_price_rules');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            'There is "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rule'),
            'There is no "Add New Rule" button on the page');
        $this->clickButton('add_new_rule');
        $this->assertFalse($this->controlIsPresent('multiselect', 'websites'),
            'There is "Store View" selector on the page');
        $this->assertTrue($this->controlIsPresent('tab', 'rule_related_banners'),
            'There is no Relates Banners tab on the page');
        $this->openTab('rule_related_banners');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_banner_visible_in'),
            'There is "Visible In" column on the page');
    }

    /**
     * <p>Shopping Cart Price Rules page does not contain websites columns and multiselects</p>
     * <p>Steps:</p>
     * <p>1.Go to Promotions - Shopping Cart Price Rules</p>
     * <p>2.Check for Website column on the Grid.</p>
     * <p>Expected result: </p>
     * <p>Website column is not displayed.</p>
     * <p>3.Click on the Add New Rule button.</p>
     * <p>4.Check for Websites multiselect</p>
     * <p>Expected result: </p>
     * <p>Websites multiselect is not displayed.</p>
     * <p>5.Click on the Related Banners tab and check "Visible In" column</p>
     * <p>Expected result: </p>
     * <p>"Visible In" column is not displayed</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6267
     * @author Tatyana_Gonchar
     */
    public function verificationShoppingCartPriceRule()
    {
        $this->admin('manage_shopping_cart_price_rules');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            'There is "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rule'),
            'There is "Add New Rule" button on the page');
        $this->clickButton('add_new_rule');
        $this->assertFalse($this->controlIsPresent('multiselect', 'websites'),
            'There is "Store View" selector on the page');
        $this->assertTrue($this->controlIsPresent('tab', 'rule_related_banners'),
            'There is no Relates Banners tab on the page');
        $this->openTab('rule_related_banners');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_banner_visible_in'),
            'There is "Visible In" column on the page');
    }

    /**
     * <p>All references to Website-Store-Store View do not displayed in the Reward Exchange Rates area</p>
     * <p>Steps:<p/>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p>
     * <p>6. Navigate to Manage Reward Exchange Rates page.</p>
     * <p>7. Click "Add New Rate" button.</p>
     * <p>Expected result:</p>
     * <p>There is no "Website" column on the page.</p>
     * <p>There is no "Website" multi selector on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6233
     * @author Nataliya_Kolenko
     */
    public function verificationRewardExchangeRates()
    {
        $this->navigate('manage_reward_rates');
        $this->assertFalse($this->controlIsPresent('dropdown', 'website'), 'There is "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rate'),
            'There is no "Add New Rate" button on the page');
        $this->clickButton('add_new_rate');
        $this->assertFalse($this->controlIsPresent('dropdown', 'website'),
            'There is "Website" multi selector on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Gift Wrapping area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p>
     * <p>6. Navigate to Manage Gift Wrapping page.</p>
     * <p>7. Click "Add Gift Wrapping" button.</p>
     * <p>Expected result:</p>
     * <p>There is no "Websites" column on the page.</p>
     * <p>There is no "Websites" multi selector on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6274
     * @author Nataliya_Kolenko
     */
    public function verificationGiftWrapping()
    {
        $this->navigate('manage_gift_wrapping');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_websites'),
            'There is "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_gift_wrapping'),
            'There is no "Add Gift Wrapping" button on the page');
        $this->clickButton('add_gift_wrapping');
        $this->assertFalse($this->controlIsPresent('multiselect', 'gift_wrapping_websites'),
            'There is "Website" multi selector on the page');
    }

    /**
     * <p>"Price" fieldset is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration - Catalog - Catalog</p>
     * <p>5.Expand Price fieldset and check for "Catalog Price Scope" dropdown </p>
     * <p>Expected result: </p>
     * <p>"Catalog Price Scope" dropdown is not displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6182
     * @author Tatyana_Gonchar
     */
    function verificationCatalogPrice()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_catalog');
        $this->assertFalse($this->controlIsPresent('dropdown', 'catalog_price_scope'),
            "Dropdown Catalog Price Scope is not present on the page");
    }
}