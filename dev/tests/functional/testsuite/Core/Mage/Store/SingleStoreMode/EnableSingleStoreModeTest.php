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
 *
 */

class Core_Mage_Store_SingleStoreMode_EnableSingleStoreModeTest extends Mage_Selenium_TestCase
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
        $this->storeHelper()->deleteStoreViewsExceptSpecified();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);

        return $userData;
    }

    /**
     * <p>Scope Selector is disabled if Single Store Mode enabled.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6180
     */
    public function systemConfigurationVerificationScopeSelector()
    {
        $this->admin('system_configuration');
        $this->assertFalse($this->controlIsPresent('fieldset', 'current_configuration_scope'),
            "Scope Selector is present on the page");
    }

    /**
     * <p>"Export Table Rates" functionality is displayed if Single Store Mode enabled.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6181
     */
    public function systemConfigurationVerificationTableRatesExport()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('sales_shipping_methods');
        $this->assertTrue($this->buttonIsPresent('table_rates_export_csv'),
            "Button Export CSV is not present on the page");
    }

    /**
     * <p>"Account Sharing Options" fieldset is displayed if Single Store Mode enabled.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6183
     */
    public function systemConfigurationVerificationAccountSharingOptions()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('customers_customer_configuration');
        $fieldset = 'account_sharing_options';
        $this->assertFalse($this->controlIsPresent('fieldset', $fieldset), "Fieldset $fieldset is present on the page");
    }

    /**
     * <p>"Price" fieldset is displayed if Single Store Mode enabled.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6183
     */
    public function systemConfigurationVerificationCatalogPrice()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_catalog');
        $this->assertFalse($this->controlIsPresent('fieldset', 'price'), "Fieldset Price is not present on the page");
    }

    /**
     * <p>"Debug" fieldset is displayed if Single Store Mode enabled.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6184
     */
    public function systemConfigurationVerificationDebugOptions()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('advanced_developer');
        $this->assertTrue($this->controlIsPresent('fieldset', 'debug'), "Fieldset Debug is not present on the page");
    }

    /**
     *<p>Hints for fields are disabled if Single Store Mode enabled.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6185
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function systemConfigurationVerificationHints()
    {
        //Skip
        $this->markTestIncomplete('MAGETWO-3502');
        //Steps
        $this->admin('system_configuration');
        $tabs = $this->getCurrentUimapPage()->getMainForm()->getAllTabs();
        foreach ($tabs as $tabName => $tabUimap) {
            $this->systemConfigurationHelper()->verifyTabFieldsAvailability($tabName);
        }
    }

    /**
     * <p> Manage Product page </p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6317
     */
    public function verificationManageProducts()
    {
        //Steps
        $this->admin('manage_products');
        //Verifying
        $this->assertFalse($this->controlIsPresent('dropdown', 'website'),
            'There is "Website" column on the page');
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->productHelper()->fillProductInfo($productData);
        //Veryfying
        $this->assertFalse($this->controlIsPresent('tab', 'websites'),
            "'Websites' tab is present on the page ");
        //Steps
        $this->productHelper()->openProductTab('prices');
        $columnsName = $this->shoppingCartHelper()->getColumnNamesAndNumbers('prices_group_price_grid_head');
        //Veryfying
        $this->assertFalse((isset($columnsName['website'])), "Group Price table contains 'Website' column");
        //Steps
        $columnsName = $this->shoppingCartHelper()->getColumnNamesAndNumbers('prices_tier_price_grid_head');
        //Veryfying
        $this->assertFalse((isset($columnsName['website'])), "Tier Price table contains 'Website' column");
    }

    /**
     * <p> Search Terms page </p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6316
     */
    public function verificationSearchTerms()
    {
        //Steps
        $this->admin('search_terms');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store'),
            'There is "Store" column on the page');
        //Steps
        $this->clickButton('add_new_search_term');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'),
            "'Store' dropdown is present on the page ");
    }

    /**
     * <p> Review and Ratings page </p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6318
     */
    public function verificationReviewRatings()
    {
        //Steps
        $this->admin('manage_pending_reviews');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is 'Visible In' column on the page");
        //Steps.
        $this->admin('manage_all_reviews');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is 'Visible In' column on the page");
        //Steps
        $this->admin('manage_ratings');
        $this->clickButton('add_new_rating');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('multiselect', 'visible_in'),
            "There is 'Visible In' multiselect on the page");
    }

    /**
     * <p> Tags page </p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6319
     */
    public function verificationTags()
    {
        //Steps
        $this->admin('all_tags');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'store_view'),
            "There is 'Store View' column on the page");
        //Steps
        $this->addParameter('storeId', '1');
        $this->clickButton('add_new_tag');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'switch_store'),
            "There is 'Store Switcher' dropdown on the page");
        //Steps
        $this->admin('pending_tags');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is 'Store View' column on the page");
    }

    /**
     * <p> URL Rewrite Management page </p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6320
     */
    public function verificationUrlRewrite()
    {
        //Steps
        $this->admin('url_rewrite_management');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store_view'),
            "There is 'Store View' column on the page");
    }

    /**
     * <p>All references to Website-Store-Store View do not displayed in the Manage Content area</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6167
     */
    public function verificationManageContent()
    {
        $this->markTestIncomplete('Skipped due to bug MAGETWO-7394');
        $this->navigate('manage_cms_pages');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_page'),
            'There is no "Add New Page" button on the page');
        $this->clickButton('add_new_page');
        $this->assertFalse($this->controlIsPresent('multiselect', 'store_view'),
            'There is "Store View" selector on the page');
        $this->clickButton('back');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is "Store View" dropdown on the page');
    }

    /**
     * <p>All references to Website-Store-Store View do not displayed in the Static Blocks area</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6168
     */
    public function verificationStaticBlocks()
    {
        $this->navigate('manage_cms_static_blocks');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_block'),
            'There is no "Add New Block" button on the page');
        $this->clickButton('add_new_block');
        $this->assertFalse($this->controlIsPresent('multiselect', 'store_view'),
            'There is "Store View" selector on the page');
        $this->clickButton('back');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is "Store View" dropdown on the page');
    }

    /**
     * <p>Assign to Store Views selector does not displayed in the New Widget Instance page</p>
     *
     * @param string $dataWidgetType
     *
     * @dataProvider widgetTypesDataProvider
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6169
     */
    public function verificationAllTypesOfWidgetsInSingleStoreMode($dataWidgetType)
    {
        $widgetData =
            $this->loadDataSet('CmsWidget', $dataWidgetType . '_settings');
        $this->navigate('manage_cms_widgets');
        $this->clickButton('add_new_widget_instance');
        $this->cmsWidgetsHelper()->fillWidgetSettings($widgetData['settings']);
        $this->assertFalse($this->controlIsPresent('multiselect', 'assign_to_store_views'),
            'There is "Store View" selector on the page');
    }

    public function widgetTypesDataProvider()
    {
        return array(
            array('cms_page_link'),
            array('cms_static_block'),
            array('catalog_category_link'),
            array('catalog_new_products_list'),
            array('catalog_product_link'),
            array('orders_and_returns'),
            array('recently_compared_products'),
            array('recently_viewed_products'),
        );
    }

    /**
     * <p>All references to Website-Store-Store View do not displayed in the Polls area</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6171
     */
    public function verificationPolls()
    {
        $this->navigate('poll_manager');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_poll'),
            'There is no "Add New Poll" button on the page');
        $this->clickButton('add_new_poll');
        $this->assertFalse($this->controlIsPresent('multiselect', 'visible_in'),
            'There is "Visible In" selector on the page');
        $this->clickButton('back');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            'There is "Visible In" dropdown on the page');
    }

    /**
     * <p>Scope Selector is not displayed on the Dashboard page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6302
     */
    public function verificationDashboardPage()
    {
        $this->navigate($this->pageAfterAdminLogin);
        $this->assertFalse($this->controlIsPresent('dropdown', 'store_switcher'),
            'There is "Choose Store View" scope selector on the page');
    }

    /**
     * <p>Create Customer Page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6229
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
     *
     * @param $userData
     * @depends preconditionsForTests
     * @test
     * @TestLinkId TL-MAGE-6230
     */
    public function editCustomer($userData)
    {
        //Steps
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

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Newsletter Subscribers area.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6311
     */
    public function verificationNewsletterSubscribers()
    {
        $this->navigate('newsletter_subscribers');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" scope selector on the page');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store'),
            'There is no "Store" scope selector on the page');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is no "Store View" scope selector on the page');
    }

    /**
     * <p>Catalog Price Rules page does not contain websites columns and multiselects</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6266
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
    }

    /**
     * <p>Shopping Cart Price Rules page does not contain websites columns and multiselects</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6267
     */
    public function verificationShoppingCartPriceRule()
    {
        $this->admin('manage_shopping_cart_price_rules');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            'There is "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rule'),
            'There is no "Add New Rule" button on the page');
        $this->clickButton('add_new_rule');
        $this->assertFalse($this->controlIsPresent('multiselect', 'websites'),
            'There is "Store View" selector on the page');
    }
    /**
     * <p>Reports</p>
     *
     * @dataProvider allReportPagesDataProvider
     * @depends preconditionsForTests
     *
     * @test
     * @TestLinkId TL-MAGE-6288
     */
    public function allReportPages($page)
    {
        //Steps
        $this->navigate($page);
        //Validation
        $this->assertFalse($this->controlIsPresent('dropdown', 'store_switcher'),
            "Dropdown associate_to_website present on page");
    }

    public function allReportPagesDataProvider()
    {
        return array(
            array('reports_sales_sales'),
            array('report_sales_tax'),
            array('report_sales_invoiced'),
            array('report_sales_shipping'),
            array('report_sales_refunded'),
            array('report_sales_coupons'),
            array('report_shopcart_abandoned'),
            array('report_sales_bestsellers'),
            array('report_product_sold'),
            array('report_product_viewed'),
            array('report_product_lowstock'),
            array('report_product_downloads'),
            array('report_customer_accounts'),
            array('report_customer_totals'),
            array('report_customer_orders'),
            array('report_tag_popular'),
        );
    }

    /**
     * <p>"Please Select a Store" step is present during New Order Creation</p>
     *
     * @param array $userData
     *
     * @depends preconditionsForTests
     * @test
     * @TestLinkId TL-MAGE-6271
     */
    public function verificationSelectStoreDuringOrderCreation($userData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->clickButton('create_new_order');
        $this->searchAndOpen(array('email' => $userData['email']), 'order_customer_grid', true);
        $this->waitForAjax();
        $this->assertFalse($this->controlIsVisible('fieldset', 'order_store_selector'),
            'There is "Please Select a Store" field set on the page');
    }

    /**
     * <p>"Store" column is not displayed on the Recurring Profiles(beta) page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6272
     */
    public function verificationRecurringProfiles()
    {
        $this->navigate('manage_sales_recurring_profile');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store'),
            'There is "Store" column on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Terms and Conditions area.</p>
     *
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6273
     */
    public function verificationTermsAndConditions()
    {
        $this->navigate('manage_sales_checkout_terms_conditions');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is no "Store View" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'create_new_terms_and_conditions'),
            'There is no "Add New Condition" button on the page');
        $this->clickButton('create_new_terms_and_conditions');
        $this->assertFalse($this->controlIsPresent('multiselect', 'store_view'),
            'There is "Store View" multi selector on the page');
    }
    /**
     * <p>All references to Website-Store-Store View are not displayed in the Schedule Design area.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6321
     */
    public function verificationDesignSchedule()
    {
        $this->navigate('system_design');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'), 'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_design_change'),
            'There is no "Add Design Change" button on the page');
        $this->clickButton('add_design_change');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'),
            'There is "Store" multi selector on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Order Statuses area.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6324
     */
    public function verificationOrderStatuses()
    {
        $this->navigate('order_statuses');
        $this->assertTrue($this->controlIsPresent('button', 'create_new_status'),
            'There is no "Create New Status" button on the page');
        $this->clickButton('create_new_status');
        $this->assertFalse($this->controlIsPresent('fieldset', 'store_view_specific_labels'),
            'There is no "Store View Specific Labels" field set on the page');
    }

}
