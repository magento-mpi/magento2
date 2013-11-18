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

class Core_Mage_Store_SingleStoreMode_DisableSingleStoreModeTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteAllStoresExceptSpecified();
        $this->assertEquals(1, $this->getTotalRecordsInTable('fieldset', 'manage_stores'));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/enable_single_store_mode');
    }

    /**
     * <p>Create customer</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        return $userData;
    }

    /**
     * <p>Scope Selector is displayed is Single Store Mode is disabled.</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6256
     */
    public function systemConfigurationVerificationScopeSelector()
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
     *
     * @test
     * @dataProvider diffScopeDataProvider
     * @TestLinkId TL-MAGE-6251
     */
    public function systemConfigurationVerificationTableRatesExport($diffScope)
    {
        $this->navigate('system_configuration');
        $this->selectStoreScope('dropdown', 'current_configuration_scope', $diffScope);
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
     *
     * @test
     * @dataProvider diffScopeDataProvider
     * @TestLinkId TL-MAGE-6253
     */
    public function systemConfigurationVerificationAccountSharingOptions($diffScope)
    {
        $this->navigate('system_configuration');
        $this->selectStoreScope('dropdown', 'current_configuration_scope', $diffScope);
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
     *
     * @test
     * @dataProvider diffScopeDataProvider
     * @TestLinkId TL-MAGE-6252
     */
    public function systemConfigurationVerificationCatalogPrice($diffScope)
    {
        $this->navigate('system_configuration');
        $this->selectStoreScope('dropdown', 'current_configuration_scope', $diffScope);
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
     *
     * @test
     * @dataProvider diffScopeDataProvider
     * @TestLinkId TL-MAGE-6254
     */
    public function systemConfigurationVerificationDebugOptions($diffScope)
    {
        $this->navigate('system_configuration');
        $this->selectStoreScope('dropdown', 'current_configuration_scope', $diffScope);
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
     *
     * @test
     * @TestLinkId TL-MAGE-6255
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function systemConfigurationVerificationHints()
    {
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
     * @TestLinkId TL-MAGE-6299
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
        $this->productHelper()->selectTypeProduct('simple');
        $this->productHelper()->fillProductInfo($productData);
        $this->productHelper()->openProductTab('prices');
        $columnsName = $this->getTableHeadRowNames($this->_getControlXpath('pageelement', 'prices_group_price_grid'));
        //Verifying
        $this->assertTrue(in_array('Web Site', $columnsName), "Group Price table not contain 'Web Site' column");
        //Steps
        $columnsName = $this->getTableHeadRowNames($this->_getControlXpath('pageelement', 'prices_tier_price_grid'));
        //Verifying
        $this->assertTrue(in_array('Web Site', $columnsName), "Tier Price table not contain 'Web Site' column");
        $this->assertTrue($this->controlIsPresent('tab', 'websites'),
            "'Websites' tab is not present on the page ");
    }

    /**
     * <p> Search Terms page </p>
     *
     * @test
     * @TestLinkId TL-MAGE-6298
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
     *
     * @test
     * @TestLinkId TL-MAGE-6300
     */
    public function verificationReviewRatings()
    {
        //Steps
        $this->admin('manage_all_reviews');
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
     *
     * @test
     * @TestLinkId TL-MAGE-6301
     */
    public function verificationTags()
    {
        $this->markTestIncomplete('Tag module is disabled');
        //Steps
        $this->navigate('all_tags');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'store_view'),
            "There is no 'Store View' column on the page");
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "There is no 'Store View' column on the page");
        //Steps
        $this->addParameter('storeId', '1');
        $this->clickButton('add_new_tag');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'choose_store_view'),
            "There is no 'Store Switcher' dropdown on the page");
    }

    /**
     * <p> URL Rewrite Management page </p>
     *
     * @test
     * @TestLinkId TL-MAGE-6306
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
     *
     * @test
     * @TestLinkId TL-MAGE-6218
     */
    public function verificationManageContent()
    {
        $this->markTestIncomplete('MAGETWO-7394');
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
     *
     * @test
     * @TestLinkId TL-MAGE-6219
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
     *
     * @param string $dataWidgetType
     *
     * @test
     * @dataProvider widgetTypesDataProvider
     * @TestLinkId TL-MAGE-6220
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
     * <p>Scope Selector is displayed on the Dashboard page.</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6303
     */
    public function verificationDashboardPage()
    {
        $this->navigate($this->pageAfterAdminLogin);
        $isPresent = $this->controlIsPresent('link', 'choose_store_view');
        if ($this->pageAfterAdminLogin == 'store_launcher') {
            $this->assertFalse($isPresent, 'There is "Choose Store View" scope selector on the page');
        } else {
            $this->assertTrue($isPresent, 'There is no "Choose Store View" scope selector on the page');
        }
    }


    /**
     * <p>Create Customer Page</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6260
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
     *
     * @param $userData
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6258
     */
    public function editCustomer($userData)
    {
        $this->markTestIncomplete('BUG: Fatal error on customer wishlist tab');
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
     *
     * @test
     * @TestLinkId TL-MAGE-6310
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
     *
     * @test
     * @TestLinkId TL-MAGE-6262
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
     *
     * @test
     * @TestLinkId TL-MAGE-6263
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
     *
     * @test
     * @dataProvider allReportPagesDataProvider
     * @TestLinkId TL-MAGE-6287
     */
    public function allReportPages($page)
    {
        $this->navigate($page);
        //Validation
        $this->assertTrue($this->controlIsPresent('dropdown', 'choose_store_view'),
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
            //array('report_tag_popular'),
        );
    }

    /**
     * <p>"Please Select a Store" step is present during New Order Creation</p>
     *
     * @param array $userData
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6275
     */
    public function verificationSelectStoreDuringOrderCreation($userData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->clickButton('create_new_order');
        $this->orderHelper()->searchAndOpen(array('email' => $userData['email']), 'order_customer_grid', true);
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('fieldset', 'order_store_selector'),
            'There is no "Please Select a Store" field set on the page');
    }

    /**
     * <p>"Store" column is displayed on the Recurring Profiles(beta) page</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6276
     */
    public function verificationRecurringProfiles()
    {
        $this->navigate('manage_sales_recurring_profile');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store'),
            'There is no "Store" column on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Terms and Conditions area.</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6277
     */
    public function verificationTermsAndConditions()
    {
        $this->navigate('manage_sales_checkout_terms_conditions');
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
     *
     * @test
     * @TestLinkId TL-MAGE-6312
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
     * <p>There is "Store View Specific Labels" field set is displayed in the Order Statuses area.</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6315
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
