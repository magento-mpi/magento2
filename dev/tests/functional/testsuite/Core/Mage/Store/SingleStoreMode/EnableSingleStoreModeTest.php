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
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);

        return $userData;
    }

    /**
     * <p>Scope Selector is disabled if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration.</p>
     * <p>Expected result: </p>
     * <p>Scope Selector is not displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6180
     * @author Tatyana_Gonchar
     */
    public function systemConfigurationVerificationScopeSelector()
    {
        $this->admin('system_configuration');
        $this->assertFalse($this->controlIsPresent('fieldset', 'current_configuration_scope'),
            "Scope Selector is present on the page");
    }

    /**
     * <p>"Export Table Rates" functionality is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - Sales - Shipping Methods.</p>
     * <p>3.Check for "Table Rates" fieldset  </p>
     * <p>Expected result: </p>
     * <p>"Export CSV" button is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6181
     * @author Tatyana_Gonchar
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
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - Customer - Customer Configuration</p>
     * <p>3.Check for "Account Sharing Options" fieldset  </p>
     * <p>Expected result:</p>
     * <p>"Account Sharing Options" fieldset is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6183
     * @author Tatyana_Gonchar
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
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - Catalog - Catalog</p>
     * <p>3.Check for "Price" fieldset</p>
     * <p>Expected result: </p>
     * <p>"Price" fieldset is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6183
     * @author Tatyana_Gonchar
     */
    public function systemConfigurationVerificationCatalogPrice()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_catalog');
        $this->assertFalse($this->controlIsPresent('fieldset','price'), "Fieldset Price is not present on the page");
    }

    /**
     * <p>"Debug" fieldset is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - Advanced - Developer</p>
     * <p>3.Check for "Debug" fieldset.</p>
     * <p>Expected result:</p>
     * <p>"Debug" fieldset is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6184
     * @author Tatyana_Gonchar
     */
    public function systemConfigurationVerificationDebugOptions()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('advanced_developer');
        $this->assertTrue($this->controlIsPresent('fieldset', 'debug'), "Fieldset Debug is not present on the page");
    }

    /**
     *<p>Hints for fields are disabled if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration</p>
     * <p>3.Open required tab and fieldset and check hints</p>
     * <p>Expected result: </p>
     * <p>Hints are not displayed</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6185
     * @author Tatyana_Gonchar
     */
    public function systemConfigurationVerificationHints()
    {
        //Skip
        $this->markTestIncomplete('MAGETWO-3502');
        //Steps
        $this->admin('system_configuration');
        $storeView = $this->_getControlXpath('pageelement', 'store_view_hint');
        $globalView = $this->_getControlXpath('pageelement', 'global_view_hint');
        $websiteView = $this->_getControlXpath('pageelement', 'website_view_hint');
        $tabs = $this->getCurrentUimapPage()->getMainForm()->getAllTabs();
        foreach ($tabs as $tab => $value) {
            $uimapFields = array();
            $this->openTab($tab);
            $uimapFields[self::FIELD_TYPE_MULTISELECT] = $value->getAllMultiselects();
            $uimapFields[self::FIELD_TYPE_DROPDOWN] = $value->getAllDropdowns();
            $uimapFields[self::FIELD_TYPE_INPUT] = $value->getAllFields();
            foreach ($uimapFields as $element) {
                foreach ($element as $name => $xpath) {
                    if ($this->isElementPresent($xpath . $storeView)) {
                        $this->addVerificationMessage("Element $name is on the page");
                    }
                    if ($this->isElementPresent($xpath . $globalView)) {
                        $this->addVerificationMessage("Element $name is on the page");
                    }
                    if ($this->isElementPresent($xpath . $websiteView)) {
                        $this->addVerificationMessage("Element $name is on the page");
                    }
                }
            }
        }
        $this->assertEmptyVerificationErrors();

    }
    /**
     * <p> Manage Product page </p>
     * <p> 1. Go to Catalog - Manage Products </p>
     * <p> 2. Check for Websites column on the Products grid.</p>
     * <p> Expected result:</p>
     * <p> Websites column is not displayed </p>
     * <p> 3. Click on the Add Product button</p>
     * <p> 4. Go to the New Product page, Prices tab</p>
     * <p> 5. Check for Website column in Group Price and Tier Price tables</p>
     * <p> Expected result: </p>
     * <p> Column is not displayed.</p>
     * <p> 6. Check Website tab on the Product Info tab set</p>
     * <p> Expected result:</p>
     * <p> Website tab is not displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6317
     * @author Tatyana_Gonchar
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
        $this->clickButton('add_new_product_split');
        $this->productHelper()->fillProductSettings($productData);
        //Veryfying
        $this->assertFalse($this->controlIsPresent('tab', 'websites'),
            "'Websites' tab is present on the page ");
        //Steps
        $this->openTab('prices');
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
     * <p> 1. Go to Catalog - Search Terms</p>
     * <p> 2. Check for Store column on the Search Terms grid.</p>
     * <p> Expected result:</p>
     * <p> Store column is not displayed </p>
     * <p> 3. Click on the Add New Search Term button</p>
     * <p> 4. Check for Store dropdown </p>
     * <p> Expected result: </p>
     * <p> Store dropdown is not displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6316
     * @author Tatyana_Gonchar
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
     * <p> 1. Go to Catalog - Review Ratings - Customer Reviews - Pending Reviews</p>
     * <p> 2. Check for Visible In column on the Pending Reviews grid.</p>
     * <p> Expected result:</p>
     * <p> Visible In column is not displayed </p>
     * <p> 3. Go to Catalog - Review Ratings - Customer Reviews - All Reviews</p>
     * <p> 4. Check for Visible In column on the All Reviews grid. </p>
     * <p> Expected result: </p>
     * <p> Visible In column is not displayed.</p>
     * <p> 5. Go to Catalog - Review Ratings - Manage Ratings</p>
     * <p> 6. Click on the Add New Rating button. </p>
     * <p> 7. Check for Visible In multiselect</p>
     * <p> Expected result: </p>
     * <p> Visible In multiselect is not displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6318
     * @author Tatyana_Gonchar
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
     * <p> 1. Go to Catalog - Tags - All Tags </p>
     * <p> 2. Check for Store View column on the Manage Tags grid.</p>
     * <p> Expected result:</p>
     * <p> Store View column is not displayed </p>
     * <p> 3. Click on the Add New Tag button. </p>
     * <p> 4. Check for Scope switcher</p>
     * <p> Expected result: </p>
     * <p> Scope switcher is not displayed.</p>
     * <p> 5. Go to Catalog - Tags - Pending Tags. </p>
     * <p> 6. Check for Store View dropdown</p>
     * <p> Expected result: </p>
     * <p> Store View dropdown is not displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6319
     * @author Tatyana_Gonchar
     */
    public function verificationTags()
    {
        //Steps
        $this->admin('all_tags');
        //Veryfying
        $this->assertFalse($this->controlIsPresent('dropdown', 'store_view'),
            "There is 'Store View' column on the page");
        //Steps
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
     * <p> 1. Go to Catalog - URL Rewrite Management </p>
     * <p> 2. Check for Store View column on the URL Rewrite Management grid.</p>
     * <p> Expected result:</p>
     * <p> Store View column is not displayed </p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6320
     * @author Tatyana_Gonchar
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
     * <p>Steps</>
     * <p>1. Navigate to Manage Pages page</p>
     * <p>2. Click "Add Mew Page" button</p>
     * <p>3. Click "Back" button</p>
     * <p>Expected result:</p>
     * <p>There is no "Store View" selector on the page</p>
     * <p>There is no "Store View" column on the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6167
     * @author Nataliya_Kolenko
     */
    public function verificationManageContent()
    {
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
     * <p>Steps:</p>
     * <p>1. Navigate to Static Blocks  page</p>
     * <p>2. Click "Add Mew Block" button</p>
     * <p>3. Click "Back" button</p>
     * <p>Expected result:</p>
     * <p>There is no "Store View" selector on the page</p>
     * <p>There is no "Store View" column on the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6168
     * @author Nataliya_Kolenko
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
     * <p>Steps:</p>
     * <p>1. Navigate to Poll Manager page</p>
     * <p>2. Click "Add Mew Poll" button</p>
     * <p>2. Click "Back" button</p>
     * <p>Expected result:</p>
     * <p>There is no "Visible In" selector on the page</p>
     * <p>There is no "Visible In" column on the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6171
     * @author Nataliya_Kolenko
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
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Dashboard page.</p>
     * <p>Expected result:</p>
     * <p>There is no "Choose Store View" scope selector on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6302
     * @author Nataliya_Kolenko
     */
    public function verificationDashboardPage()
    {
        $this->navigate('dashboard');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store_switcher'),
            'There is "Choose Store View" scope selector on the page');
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
     * @depends preconditionsForTests
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
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Newsletter Subscribers page.</p>
     * <p>Expected result:</p>
     * <p>There is no "Website" multi selector on the page.</p>
     * <p>There is no "Store" multi selector on the page.</p>
     * <p>There is no "Store View" multi selector on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6311
     * @author Nataliya_Kolenko
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
     * <p>Steps:</p>
     * <p>1.Go to Promotions - Catalog Price Rules
     * <p>2.Check for Website column on the Grid.
     * <p>Expected result: </p>
     * <p>Website column is not displayed.</p>
     * <p>3.Click on the Add New Rule button.</p>
     * <p>4.Check for Websites multiselect</p>
     * <p>Expected result: </p>
     * <p>Websites multiselect is not displayed.</p>
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
    }

    /**
     * <p>Shopping Cart Price Rules page does not contain websites columns and multiselects</p>
     * <p>Steps:</p>
     * <p>1.Go to Promotions - Shopping Cart Price Rules
     * <p>2.Check for Website column on the Grid.
     * <p>Expected result: </p>
     * <p>Website column is not displayed.</p>
     * <p>3.Click on the Add New Rule button.</p>
     * <p>4.Check for Websites multiselect</p>
     * <p>Expected result: </p>
     * <p>Websites multiselect is not displayed.</p>
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
            'There is no "Add New Rule" button on the page');
        $this->clickButton('add_new_rule');
        $this->assertFalse($this->controlIsPresent('multiselect', 'websites'),
            'There is "Store View" selector on the page');
    }
    /**
     * <p>Reports</p>
     * <p>Preconditions</p>
     * <p>Magento contain only one store view</p>
     * <p>Disable single store mode System->Configuration->General->General->Single-Store Mode</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Go to Reports pages</p>
     * <p>3. Verify that on all reports pages Scope Selector is missing</p>
     * <p>Expected Result</p>
     * <p>Scope Selector is is missing on reports pages</p>
     *
     * @dataProvider allReportPagesDataProvider
     * @depends preconditionsForTests
     *
     * @test
     * @TestlinkId TL-MAGE-6288
     * @author Maksym_Iakusha
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
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p>
     * <p>6. Navigate to Orders page.</p>
     * <p>7. Click "Create New Order" button.</p>
     * <p>8. Choose any customer.</p>
     * <p>Expected result:</p>
     * <p>There is no "Please Select a Store" field set on the page</p>
     *
     * @param array $userData
     *
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-6271
     * @author Nataliya_Kolenko
     */
    public function verificationSelectStoreDuringOrderCreation($userData)
    {
        //Data
        $param = $userData['first_name'] . ' ' . $userData['last_name'];
        $this->addParameter('customer_first_last_name', $param);
        $this->navigate('manage_sales_orders');
        $this->clickButton('create_new_order');
        $this->orderHelper()->searchAndOpen(array('email' => $userData['email']), false, 'order_customer_grid');
        $this->waitForAjax();
        $this->assertFalse($this->controlIsVisible('fieldset', 'order_store_selector'),
            'There is "Please Select a Store" field set on the page');
    }

    /**
     * <p>"Store" column is not displayed on the Recurring Profiles(beta) page</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p>
     * <p>6. Navigate to Recurring Profiles(beta) page.</p>
     * <p>Expected result:</p>
     * <p>There is no "Store" column the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6272
     * @author Nataliya_Kolenko
     */
    public function verificationRecurringProfiles()
    {
        $this->navigate('manage_sales_recurring_profile');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store'),
            'There is "Store" column on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Terms and Conditions area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p></p>
     * <p>6. Navigate to "Manage Terms and Conditions" page.</p>
     * <p>7. Click "Add New Condition" button".</p>
     * <p>Expected result:</p>
     * <p>There is no "Store View" column on the page.</p>
     * <p>There is no "Store View" multi selector on the page.</p>
     *
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6273
     * @author Nataliya_Kolenko
     */
    public function verificationTermsAndConditions()
    {
        $this->navigate('manage_checkout_terms_and_conditions');
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
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Design page.</p>
     * <p>5. Click "Add Design Change" button.</p>
     * <p>Expected result:</p>
     * <p>There is no "Store" column on the page.</p>
     * <p>There is no "Store" multi selector on the "New Design Change" page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6321
     * @author Nataliya_Kolenko
     */
    public function verificationDesignSchedule()
    {
        $this->navigate('system_design');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'), 'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_design_change'),
            'There is no "Add Design Change" button on the page');
        $this->clickButton('add_design_change');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'),
            'There is no "Store" multi selector on the page');
    }

    /**
     * <p>"Content Information" field set is not displayed in the Design-Editor area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Visual Design Editor page.</p>
     * <p>Expected result:</p>
     * <p>There is no "Content Information" field set on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6322
     * @author Nataliya_Kolenko
     */
    public function verificationDesignEditor()
    {
        $this->navigate('system_design_editor');
        $this->assertFalse($this->controlIsPresent('fieldset', 'context_information'),
            'There is no "Content Information" field set on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Import Export-Dataflow-Profiles area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Profiles page.</p>
     * <p>5. Click "Add New Profile" button.</p>
     * <p>Expected result:</p>
     * <p>There is no "Store" column on the page.</p>
     * <p>There is no "Store" multi selector on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6323
     * @author Nataliya_Kolenko
     */
    public function verificationImportExportDataflowProfiles()
    {
        $this->navigate('system_convert_gui');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'), 'There is no "Store" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_profile'),
            'There is no "Add New Profile" button on the page');
        $this->clickButton('add_new_profile');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'),
            'There is no "Store" multi selector on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Order Statuses area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Order Statuses page.</p>
     * <p>5. Click "Create New Status" button.</p>
     * <p>Expected result:</p>
     * <p>There is no "Store View Specific Labels" field set on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6324
     * @author Nataliya_Kolenko
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