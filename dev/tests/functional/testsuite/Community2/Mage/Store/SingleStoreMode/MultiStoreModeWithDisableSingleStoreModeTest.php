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

class Community2_Mage_Store_SingleStoreMode_MultiStoreModeWithDisableSingleStoreModeTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Create customer and Store view </p>
     *
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
        $tableXpath = $this->_getControlXpath('pageelement', 'stores_table');
        $titleRowCount = $this->getXpathCount($tableXpath . '//tr[@title]');
        $columnId = $this->getColumnIdByName('Store View Name') - 1;
        $storeViews = array();
        for ($rowId = 0; $rowId < $titleRowCount; $rowId++) {
            $storeView = $this->getTable($tableXpath . '.' . $rowId . '.' . $columnId);
            if (!in_array($storeView, array('Default Store View'))) {
                $storeViews[] = $storeView;
            }
        }
        $isEmpty = array_filter($storeViews);
        if (empty($isEmpty)){
            $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
            $this->storeHelper()->createStore($storeViewData, 'store_view');
        }
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');

        return $userData;
    }

    /**
     * <p>Scope Selector is displayed is Single Store Mode is disabled.</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Configuration
     * <p>Expected result: </p>
     * <p>Scope Selector is displayed.</p>
     * <p>2.Repeat previous step with all different scope (Main Website, Default Store View).</p>
     * <p>Expected result: </p>
     * <p>Scope Selector is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6256
     * @author Tatyana_Gonchar
     */
    function systemConfigurationVerificationScopeSelector()
    {
        $this->admin('system_configuration');
        $this->assertTrue($this->controlIsPresent('fieldset', 'current_configuration_scope'),
            'There is no Scope Selector');
    }

    public function diffScopeDataProvider()
    {
        return array(
            array('Main Website'),
            array('Default Store View'),
            array('Default Config')
        );
    }

    /**
     * <p>"Export Table Rates" functionality is enabled only on Website scope.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Select "Main Website" on the scope switcher</p>
     * <p>3.Go to Sales - Shipping Methods.</p>
     * <p>4.Check for "Table Rates" fieldset.</p>
     * <p>Expected result: </p>
     * <p>"Export CSV" button is displayed.</p>
     * <p>5.Change the scope to "Default Store View" or "Default Config".</p>
     * <p>Expected result: </p>
     * <p>"Export CSV" button is not displayed.</p>
     *
     * @dataProvider diffScopeDataProvider
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6251
     * @author Tatyana_Gonchar
     */
    function systemConfigurationVerificationTableRatesExport($diffScope)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', $diffScope);
        $this->systemConfigurationHelper()->openConfigurationTab('sales_shipping_methods');
        $button = 'table_rates_export_csv';
        if ($diffScope == 'Main Website') {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page on $diffScope");
            }
        } else {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page on $diffScope");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>"Account Sharing Options" functionality is enabled only on Default Config scope.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Select "Default Config" on the scope switcher</p>
     * <p>3.Go to Customer - Customer Configuration.</p>
     * <p>4.Check for "Account Sharing Options" fieldset.</p>
     * <p>Expected result: </p>
     * <p>"Account Sharing Options" fieldset is displayed.</p>
     * <p>5.Change the scope to "Main Website" or "Default Store View".</p>
     * <p>Expected result: </p>
     * <p>"Account Sharing Options" fieldset is not displayed.</p>
     *
     * @dataProvider diffScopeDataProvider
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6253
     * @author Tatyana_Gonchar
     */
    function systemConfigurationVerificationAccountSharingOptions($diffScope)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', $diffScope);
        $this->systemConfigurationHelper()->openConfigurationTab('customers_customer_configuration');
        $fieldset = 'account_sharing_options';
        if ($diffScope == 'Default Config') {
            if (!$this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is not present on the page on $diffScope");
            }
        } else {
            if ($this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is present on the page on $diffScope");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>"Price" fieldset is displayed only on Default Config scope.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Select "Default Config" on the scope switcher</p>
     * <p>3.Go to Catalog - Catalog.</p>
     * <p>4.Check for "Price" fieldset.</p>
     * <p>Expected result: </p>
     * <p>Price" fieldset is displayed.</p>
     * <p>5.Change the scope to "Main Website" or "Default Store View".</p>
     * <p>Expected result: </p>
     * <p>"Price" fieldset is not displayed.</p>
     *
     * @dataProvider diffScopeDataProvider
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6252
     * @author Tatyana_Gonchar
     */
    function systemConfigurationVerificationCatalogPrice($diffScope)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', $diffScope);
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_catalog');
        $fieldset = 'price';
        if ($diffScope == 'Default Config') {
            if (!$this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is not present on the page on $diffScope");
            }
        } else {
            if ($this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is present on the page on $diffScope");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Debug" fieldset is displayed only on Main Website and Default Store View scopes.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Select "Main Website" on the scope switcher</p>
     * <p>3.Go to Advanced - Developer.</p>
     * <p>4.Check for "Debug" fieldset.</p>
     * <p>Expected result: </p>
     * <p>"Debug" fieldset is displayed.</p>
     * <p>5.Change the scope to "Default Store View" </p>
     * <p>Expected result: </p>
     * <p>"Debug" fieldset is displayed.</p>
     * <p>6.Change the scope to "Default Config" </p>
     * <p>Expected result: </p>
     * <p>"Debug" fieldset is not displayed.</p>
     *
     * @dataProvider diffScopeDataProvider
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6254
     * @author Tatyana_Gonchar
     */
    function systemConfigurationVerificationDebugOptions($diffScope)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', $diffScope);
        $this->systemConfigurationHelper()->openConfigurationTab('advanced_developer');
        $fieldset = 'debug';
        if (($diffScope == 'Main Website') || ($diffScope == 'Default Store View')) {
            if (!$this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is not present on the page on $diffScope");
            }
        } else {
            if ($this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is present on the page on $diffScope");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Hints for fields are enabled if Single Store Mode disabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Open required tab and fieldset and check hints </p>
     * <p>Expected result: </p>
     * <p>Hints are displayed</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6255
     * @author Tatyana_Gonchar
     */
    function systemConfigurationVerificationHints()
    {
        //Skip
        $this->markTestIncomplete('MAGETWO-3502');
        //Steps
        $storeView = $this->_getControlXpath('pageelement', 'store_view_hint');
        $globalView = $this->_getControlXpath('pageelement', 'global_view_hint');
        $websiteView = $this->_getControlXpath('pageelement', 'website_view_hint');
        $this->admin('system_configuration');
        $tabs = $this->getCurrentUimapPage()->getMainForm()->getAllTabs();
        foreach ($tabs as $tab => $value) {
            $uimapFields = array();
            $this->openTab($tab);
            $uimapFields[self::FIELD_TYPE_MULTISELECT] = $value->getAllMultiselects();
            $uimapFields[self::FIELD_TYPE_DROPDOWN] = $value->getAllDropdowns();
            $uimapFields[self::FIELD_TYPE_INPUT] = $value->getAllFields();
            foreach ($uimapFields as $element) {
                foreach ($element as $name => $xpath) {
                    if ((!$this->isElementPresent($xpath . $storeView)) && (!$this->isElementPresent($xpath . $globalView)) &&
                        (!$this->isElementPresent($xpath . $websiteView))) {
                        $this->addVerificationMessage("Element $name is not on the page");
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
     * <p> Websites column is displayed </p>
     * <p> 3. Click on the Add Product button</p>
     * <p> 4. Go to the New Product page, Prices tab</p>
     * <p> 5. Check for Website column in Group Price and Tier Price tables</p>
     * <p> Expected result: </p>
     * <p> Column is displayed.</p>
     * <p> 6. Check Website tab on the Product Info tab set</p>
     * <p> Expected result:</p>
     * <p> Website tab is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6299
     * @author Tatyana_Gonchar
     */
    public function verificationManageProducts()
    {
        //Steps
        $this->admin('manage_products');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'website'),
            'There is no "Website" column on the page');
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->clickButton('add_new_product_split');
        $this->productHelper()->fillProductSettings($productData);
        $this->openTab('prices');
        $columnsName = $this->shoppingCartHelper()->getColumnNamesAndNumbers('prices_group_price_grid_head');
        //Verifying
        $this->assertTrue((isset($columnsName['website'])), "Group Price table not contain 'Website' column");
        //Steps
        $columnsName = $this->shoppingCartHelper()->getColumnNamesAndNumbers('prices_tier_price_grid_head');
        //Verifying
        $this->assertTrue((isset($columnsName['website'])), "Tier Price table not contain 'Website' column");
        $this->assertTrue($this->controlIsPresent('tab', 'websites'),
            "'Websites' tab is not present on the page ");
    }

    /**
     * <p> Search Terms page </p>
     * <p> 1. Go to Catalog - Search Terms</p>
     * <p> 2. Check for Store column on the Search Terms grid.</p>
     * <p> Expected result:</p>
     * <p> Store column is displayed </p>
     * <p> 3. Click on the Add New Search Term button</p>
     * <p> 4. Check for Store dropdown </p>
     * <p> Expected result: </p>
     * <p> Store dropdown is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6298
     * @author Tatyana_Gonchar
     */
    public function verificationSearchTerms()
    {
        //Steps
        $this->navigate('search_terms');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store'),
            'There is no "Store" column on the page');
        //Steps
        $this->clickButton('add_new_search_term');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'store'),
            "'Store' dropdown is not present on the page ");
    }

    /**
     * <p> Review and Ratings page </p>
     * <p> 1. Go to Catalog - Review Ratings - Customer Reviews - Pending Reviews</p>
     * <p> 2. Check for Visible In column on the Pending Reviews grid.</p>
     * <p> Expected result:</p>
     * <p> Visible In column is displayed </p>
     * <p> 3. Go to Catalog - Review Ratings - Customer Reviews - All Reviews</p>
     * <p> 4. Check for Visible In column on the All Reviews grid. </p>
     * <p> Expected result: </p>
     * <p> Visible In column is displayed.</p>
     * <p> 5. Go to Catalog - Review Ratings - Manage Ratings</p>
     * <p> 6. Click on the Add New Rating button. </p>
     * <p> 7. Check for Visible In multiselect</p>
     * <p> Expected result: </p>
     * <p> Visible In multiselect is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6300
     * @author Tatyana_Gonchar
     */
    public function verificationReviewRatings()
    {
        //Steps
        $this->admin('manage_pending_reviews');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is no 'Visible In' column on the page");
        //Steps
        $this->admin('manage_all_reviews');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is no 'Visible In' column on the page");
        //Steps
        $this->admin('manage_ratings');
        $this->clickButton('add_new_rating');
        //Verifying
        $this->assertTrue($this->controlIsPresent('multiselect', 'visible_in'),
            "There is no 'Visible In' multiselect on the page");
    }

    /**
     * <p> Tags page </p>
     * <p> 1. Go to Catalog - Tags - All Tags </p>
     * <p> 2. Check for Store View column on the Manage Tags grid.</p>
     * <p> Expected result:</p>
     * <p> Store View column is displayed </p>
     * <p> 3. Click on the Add New Tag button. </p>
     * <p> 4. Check for Scope switcher</p>
     * <p> Expected result: </p>
     * <p> Scope switcher is displayed.</p>
     * <p> 5. Go to Catalog - Tags - Pending Tags. </p>
     * <p> 6. Check for Store View dropdown</p>
     * <p> Expected result: </p>
     * <p> Store View dropdown is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6301
     * @author Tatyana_Gonchar
     */
    public function verificationTags()
    {
        //Steps
        $this->admin('all_tags');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'store_view'),
            "There is no 'Store View' column on the page");
        //Steps
        $this->clickButton('add_new_tag');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'switch_store'),
            "There is no 'Store Switcher' dropdown on the page");
        //Steps
        $this->admin('pending_tags');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is no 'Store View' column on the page");
    }

    /**
     * <p> URL Rewrite Management page </p>
     * <p> 1. Go to Catalog - URL Rewrite Management </p>
     * <p> 2. Check for Store View column on the URL Rewrite Management grid.</p>
     * <p> Expected result:</p>
     * <p> Store View column is displayed </p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6306
     * @author Tatyana_Gonchar
     */
    public function verificationUrlRewrite()
    {
        //Steps
        $this->admin('url_rewrite_management');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store_view'),
            "There is no 'Store View' column on the page");
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Manage Content area</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Pages page</p>
     * <p>2. Click "Add Mew Page" button</p>
     * <p>3. Click "Back" button</p>
     * <p>Expected result:</p>
     * <p>There is "Store View" selector on the page</p>
     * <p>There is "Store View" column on the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6218
     * @author Nataliya_Kolenko
     */
    public function verificationManageContent()
    {
        $this->navigate('manage_cms_pages');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_page'),
            'There is no "Add New Page" button on the page');
        $this->clickButton('add_new_page');
        $this->assertTrue($this->controlIsPresent('multiselect', 'store_view'),
            'There is no "Store View" selector on the page');
        $this->clickButton('back');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is no "Store View" dropdown on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Static Blocks area</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Static Blocks  page</p>
     * <p>2. Click "Add Mew Block" button</p>
     * <p>3. Click "Back" button</p>
     * <p>Expected result:</p>
     * <p>There is "Store View" selector on the page</p>
     * <p>There is "Store View" column on the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6219
     * @author Nataliya_Kolenko
     */
    public function verificationStaticBlocks()
    {
        $this->navigate('manage_cms_static_blocks');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_block'),
            'There is no "Add New Block" button on the page');
        $this->clickButton('add_new_block');
        $this->assertTrue($this->controlIsPresent('multiselect', 'store_view'),
            'There is no "Store View" selector on the page');
        $this->clickButton('back');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is no "Store View" dropdown on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Widget area</p>
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
     * @TestlinkId TL-MAGE-6220
     * @author Nataliya_Kolenko
     */
    public function verificationAllTypesOfWidgetsInSingleStoreMode($dataWidgetType)
    {
        $widgetData = $this->loadDataSet('CmsWidget', $dataWidgetType . '_settings');
        $this->navigate('manage_cms_widgets');
        $this->clickButton('add_new_widget_instance');
        $this->cmsWidgetsHelper()->fillWidgetSettings($widgetData['settings']);
        $this->assertTrue($this->controlIsPresent('multiselect', 'assign_to_store_views'),
            'There is no "Store View" selector on the page');
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
     * <p>All references to Website-Store-Store View are displayed in the Polls area</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Poll Manager page</p>
     * <p>2. Click "Add Mew Poll" button</p>
     * <p>2. Click "Back" button</p>
     * <p>Expected result:</p>
     * <p>There is "Visible In" selector on the page</p>
     * <p>There is "Visible In" column on the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6222
     * @author Nataliya_Kolenko
     */
    public function verificationPolls()
    {
        $this->navigate('poll_manager');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_poll'),
            'There is no "Add New Poll" button on the page');
        $this->clickButton('add_new_poll');
        $this->assertTrue($this->controlIsPresent('multiselect', 'visible_in'),
            'There is no "Visible In" selector on the page');
        $this->clickButton('back');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            'There is no "Visible In" dropdown on the page');
    }

    /**
     * <p>Scope Selector is displayed on the Dashboard page.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Dashboard page.</p>
     * <p>Expected result:</p>
     * <p>There is "Choose Store View" scope selector on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6303
     * @author Nataliya_Kolenko
     */
    public function verificationDashboardPage()
    {
        $this->navigate('dashboard');
        $this->assertTrue($this->controlIsPresent('dropdown', 'store_switcher'),
            'There is no "Choose Store View" scope selector on the page');
    }


    /**
     * <p>Create Customer Page</p>
     * <p>Magento contain only one store view</p>
     * <p>Disable single store mode System->Configuration->General->General->Single-Store Mode</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Go to Customer->Manege Customer</p>
     * <p>3. Click "Add new customer" button</p>
     * <p>4. Verify fields in account information tab</p>
     * <p>Expected Result</p>
     * <p>1. Dropdowns "Associate to Website" and "Send From" are present</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6260
     * @author Maksym_Iakusha
     */
    public function newCustomer()
    {
        $this->admin('manage_customers');
        $this->clickButton('add_new_customer');
        //Validation
        $this->assertTrue($this->controlIsPresent('dropdown', 'associate_to_website'),
            "Dropdown associate_to_website present on page");
        $this->assertTrue($this->controlIsPresent('dropdown', 'send_from'), "Dropdown send_from present on page");
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
     * <p>Sales statistic grid contain "Website", "Store", "Store View" columns</p>
     * <p>Account information tab contain "Associate to Website" dropdown</p>
     * <p>Table on Orders tab is contain "Bought From" column</p>
     * <p>Table on Recurring Profile is contain "Store" column</p>
     * <p>Table on Wishlist tab is contain "Added From" column</p>
     * <p>Table on Product Review tab is contain "Visible In" Column</p>
     * <p>Expected Result</p>
     * <p>1. All of the above elements are missing</p>
     *
     * @param $userData
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6258
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
        $this->assertTrue((isset($columnsName['website']) && isset($columnsName['store'])
                           && isset($columnsName['store_view'])), "Sales Statistics table contain unnecessary column");
        $this->openTab('account_information');
        $this->assertTrue($this->controlIsPresent('dropdown', 'associate_to_website'),
            "Dropdown associate_to_website present on page");
        $this->openTab('orders');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_bought_from'),
            "Table contain 'bought_from' column");
        $this->openTab('recuring_profiles');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store'), "Table contain 'store' column");
        $this->openTab('wishlist');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_added_from'),
            "Table contain 'added_from' column");
        $this->openTab('product_review');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "Table contain 'visible_in' column");
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Newsletter Subscribers area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Newsletter Subscribers page.</p>
     * <p>Expected result:</p>
     * <p>There is "Website" multi selector on the page.</p>
     * <p>There is "Store" multi selector on the page.</p>
     * <p>There is "Store View" multi selector on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6310
     * @author Nataliya_Kolenko
     */
    public function verificationNewsletterSubscribers()
    {
        $this->navigate('newsletter_subscribers');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" scope selector on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store'),
            'There is no "Store" scope selector on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is no "Store View" scope selector on the page');
    }

    /**
     * <p>Catalog Price Rules page contains websites columns and multiselects</p>
     * <p>Steps:</p>
     * <p>1.Go to Promotions - Catalog Price Rules
     * <p>2.Check for Website column on the Grid.
     * <p>Expected result: </p>
     * <p>Website column is displayed.</p>
     * <p>3.Click on the Add New Rule button.</p>
     * <p>4.Check for Websites multiselect</p>
     * <p>Expected result: </p>
     * <p>Websites multiselect is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6262
     * @author Tatyana_Gonchar
     */
    public function verificationCatalogPriceRule()
    {
        $this->admin('manage_catalog_price_rules');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rule'),
            'There is no "Add New Rule" button on the page');
        $this->clickButton('add_new_rule');
        $this->assertTrue($this->controlIsPresent('multiselect', 'websites'),
            'There is no "Store View" selector on the page');
    }

    /**
     * <p>Shopping Cart Price Rules page contains websites columns and multiselects</p>
     * <p>Steps:</p>
     * <p>1.Go to Promotions - Shopping Cart Price Rules
     * <p>2.Check for Website column on the Grid.
     * <p>Expected result: </p>
     * <p>Website column is displayed.</p>
     * <p>3.Click on the Add New Rule button.</p>
     * <p>4.Check for Websites multiselect</p>
     * <p>Expected result: </p>
     * <p>Websites multiselect is displayed.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6263
     * @author Tatyana_Gonchar
     */
    public function verificationShoppingCartPriceRule()
    {
        $this->admin('manage_shopping_cart_price_rules');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rule'),
            'There is no "Add New Rule" button on the page');
        $this->clickButton('add_new_rule');
        $this->assertTrue($this->controlIsPresent('multiselect', 'websites'),
            'There is no "Store View" selector on the page');
    }

    /**
     * <p>Reports</p>
     * <p>Preconditions</p>
     * <p>Magento contain only one store view</p>
     * <p>Disable single store mode System->Configuration->General->General->Single-Store Mode</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Go to Reports pages</p>
     * <p>3. Verify that all reports pages contain Scope Selector</p>
     * <p>Expected Result</p>
     * <p>Scope Selector is show on reports pages</p>
     *
     * @dataProvider allReportPagesDataProvider
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6287
     * @author Maksym_Iakusha
     */
    public function allReportPages($page)
    {
        $this->navigate($page);
        //Validation
        $this->assertTrue($this->controlIsPresent('dropdown', 'store_switcher'),
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
     * <p>5. Configure Enable Single-Store Mode - No.</p>
     * <p>6. Navigate to Orders page.</p>
     * <p>5. Click "Create New Order" button.</p>
     * <p>6. Choose any customer.</p>
     * <p>Expected result:</p>
     * <p>There is "Please Select a Store" field set on the page</p>
     *
     * @param array $userData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6275
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
        $this->assertTrue($this->controlIsPresent('fieldset', 'order_store_selector'),
            'There is no "Please Select a Store" field set on the page');
    }

    /**
     * <p>"Store" column is displayed on the Recurring Profiles(beta) page</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - No.</p>
     * <p>6. Navigate to Recurring Profiles(beta) page.</p>
     * <p>Expected result:</p>
     * <p>There is "Store" column the page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6276
     * @author Nataliya_Kolenko
     */
    public function verificationRecurringProfiles()
    {
        $this->navigate('manage_sales_recurring_profile');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store'),
            'There is no "Store" column on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Terms and Conditions area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - No.</p></p>
     * <p>6. Navigate to "Manage Terms and Conditions" page.</p>
     * <p>7. Click "Add New Condition" button".</p>
     * <p>Expected result:</p>
     * <p>There is "Store View" column on the page.</p>
     * <p>There is "Store View" multi selector on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6277
     * @author Nataliya_Kolenko
     */
    public function verificationTermsAndConditions()
    {
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is no "Store View" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'create_new_terms_and_conditions'),
            'There is no "Add New Condition" button on the page');
        $this->clickButton('create_new_terms_and_conditions');
        $this->assertTrue($this->controlIsPresent('multiselect', 'store_view'),
            'There is no "Store View" multi selector on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Schedule Design area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Design page.</p>
     * <p>5. Click "Add Design Change" button.</p>
     * <p>Expected result:</p>
     * <p>There is "Store" column on the page.</p>
     * <p>There is "Store" multi selector on the "New Design Change" page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6312
     * @author Nataliya_Kolenko
     */
    public function verificationDesignSchedule()
    {
        $this->navigate('system_design');
        $this->assertTrue($this->controlIsPresent('dropdown', 'store'), 'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_design_change'),
            'There is no "Add Design Change" button on the page');
        $this->clickButton('add_design_change');
        $this->assertTrue($this->controlIsPresent('dropdown', 'store'),
            'There is no "Store" multi selector on the page');
    }

    /**
     * <p>"Content Information" field set is displayed in the Design-Editor area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Visual Design Editor page.</p>
     * <p>Expected result:</p>
     * <p>There is "Content Information" field set on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6313
     * @author Nataliya_Kolenko
     */
    public function verificationDesignEditor()
    {
        $this->navigate('system_design_editor');
        $this->assertTrue($this->controlIsPresent('fieldset', 'context_information'),
            'There is no "Content Information" field set on the page');
    }

    /**
     * <p>There is "Store View Specific Labels" field set is displayed in the Order Statuses area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Order Statuses page.</p>
     * <p>5. Click "Create New Status" button.</p>
     * <p>Expected result:</p>
     * <p>There is "Store View Specific Labels" field set on the page.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6315
     * @author Nataliya_Kolenko
     */
    public function verificationOrderStatuses()
    {
        $this->navigate('order_statuses');
        $this->assertTrue($this->controlIsPresent('button', 'create_new_status'),
            'There is no "Create New Status" button on the page');
        $this->clickButton('create_new_status');
        $this->assertTrue($this->controlIsPresent('fieldset', 'store_view_specific_labels'),
            'There is no "Store View Specific Labels" field set on the page');
    }
}
