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

class Enterprise_Mage_Store_SingleStoreMode_DisableSingleStoreModeTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteAllStoresExceptSpecified();
        $this->assertEquals(1, $this->getTotalRecordsInTable('fieldset', 'manage_stores'));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
        $this->systemConfigurationHelper()->configure('CustomerSegment/enable_customer_segment');
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
     * <p>Choose Scope selector is displayed on the Manage Page Hierarchy page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6217
     */
    public function verificationManageHierarchy()
    {
        $this->navigate('manage_pages_hierarchy');
        $this->assertTrue($this->controlIsPresent('dropdown', 'choose_store_view'),
            'There is no "Choose Scope" selector on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Widget area</p>
     *
     * @param string $dataWidgetType
     * @dataProvider widgetTypesDataProvider
     *
     * @test
     * @depends preconditionsForTests
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
            array('banner_rotator'),
            array('cms_hierarchy_node_link'),
            array('catalog_events_carousel'),
            array('gift_registry_search'),
            array('order_by_sku'),
            array('wishlist_search'),
        );
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Banner area</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6221
     */
    public function verificationBanners()
    {
        $this->navigate('manage_cms_banners');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_banner'),
            'There is no "Add Banner" button on the page');
        $this->clickButton('add_new_banner');
        $this->assertTrue($this->controlIsPresent('tab', 'content'), 'There is Content tab on the page');
        $this->openTab('content');
        $this->assertTrue($this->controlIsPresent('fieldset', 'specific_content'),
            'There is "Store View Specific Content" selector on the page');
        $this->clickButton('back');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            'There is "Visible In" dropdown on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Customer Segments area</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6227
     */
    public function verificationCustomerSegments()
    {
        $this->navigate('manage_customer_segments');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_segment'),
            'There is no "Add Segment" button on the page');
        $this->clickButton('add_new_segment');
        $this->assertTrue($this->controlIsPresent('multiselect', 'assigned_to_website'),
            'There is no "Assigned to Website" selector on the page');
    }

    /**
     * <p>Edit Customer Page</p>
     *
     * @param $userData
     * @depends preconditionsForTests
     * @test
     * @TestLinkId TL-MAGE-6258
     */
    public function editCustomer($userData)
    {
        $this->markTestIncomplete('BUG: Fatal error on customer wishlist tab');
        //Preconditions
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $rewardGrid = $this->getTableHeadRowNames($this->_getControlXpath('fieldset', 'reward_points'));
        $this->assertTrue(in_array('Website', $rewardGrid), "Reward Points table not contain 'Website' column");
        $storeCreditGrid = $this->getTableHeadRowNames($this->_getControlXpath('fieldset', 'store_credit_balance'));
        $this->assertTrue(in_array('Website', $storeCreditGrid), "Store Credit table not contain 'Website' column");
        $salesGrid = $this->getTableHeadRowNames($this->_getControlXpath('fieldset', 'sales_statistics'));
        $this->assertTrue(in_array('Website', $salesGrid), "Sales Statistics table not contain 'Website' column");
        $this->openTab('account_information');
        $this->assertTrue($this->controlIsPresent('dropdown', 'associate_to_website'),
            "Dropdown associate_to_website is not present on page");
        $this->openTab('orders');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_bought_from'),
            "Table is not contain 'bought_from' column");
        $this->openTab('recuring_profiles');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store'), "Table is not contain 'store' column");
        $this->openTab('wishlist');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_added_from'),
            "Table is not contain 'added_from' column");
        $this->openTab('product_review');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            "Table is not contain 'visible_in' column");
        $this->openTab('store_credit');
        $storeCredit2 = $this->shoppingCartHelper()->getColumnNamesAndNumbers('store_credit_balance_head');
        $this->assertTrue((isset($storeCredit2['website'])), "Store Credit table is not contain 'website' column");
        $this->assertTrue($this->controlIsPresent('dropdown', 'website'), "Dropdown 'website' is absent");
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'), "Dropdown 'filter_website' is absent");
        $this->openTab('gift_regystry');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'), "Dropdown 'filter_website' is absent");
        $this->openTab('reward_points');
        $rewardGrid2 = $this->shoppingCartHelper()->getColumnNamesAndNumbers('reward_points_balance_head');
        $this->assertTrue((isset($rewardGrid2['website'])), "Reward Points table is not contain 'website' column");
        $this->assertTrue($this->controlIsPresent('dropdown', 'store'), "Dropdown 'store' is absent");
        $this->clickControl('link', 'reward_points_history_link', false);
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'), "Dropdown 'filter_website' is absent");
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Gift Card Account area.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6239
     */
    public function verificationGiftCardAccounts()
    {
        $this->navigate('manage_gift_card_account');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" multi selector on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_gift_card_account'),
            'There is no "Add Gift Card Account" button on the page');
        $this->clickButton('add_gift_card_account');
        $this->assertTrue($this->controlIsPresent('multiselect', 'website'),
            'There is no "Website" multi selector on the page');
        $this->assertTrue($this->controlIsPresent('tab', 'send_gift_card'),
            'There is no Send Gift Card tab on the page');
        $this->openTab('send_gift_card');
        $this->assertTrue($this->controlIsPresent('multiselect', 'send_email_from'),
            'There is no Send Email from the Following Store View multi selector on the page');
    }

    /**
     * <p>Catalog Price Rules page contains websites columns and multiselects</p>
     *
     * @test
     * @depends preconditionsForTests
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
        $this->assertTrue($this->controlIsPresent('tab', 'rule_related_banners'),
            'There is no Relates Banners tab on the page');
        $this->openTab('rule_related_banners');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_banner_visible_in'),
            'There is no "Visible In" column on the page');
    }

    /**
     * <p>Shopping Cart Price Rules page contains websites columns and multiselects</p>
     *
     * @test
     * @depends preconditionsForTests
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
        $this->assertTrue($this->controlIsPresent('tab', 'rule_related_banners'),
            'There is no Relates Banners tab on the page');
        $this->openTab('rule_related_banners');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_banner_visible_in'),
            'There is no "Visible In" column on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Reward Exchange Rates area</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6234
     */
    public function verificationRewardExchangeRates()
    {
        $this->navigate('manage_reward_rates');
        $this->assertTrue($this->controlIsPresent('dropdown', 'website'), 'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rate'),
            'There is no "Add New Rate" button on the page');
        $this->clickButton('add_new_rate');
        $this->assertTrue($this->controlIsPresent('dropdown', 'website'),
            'There is no "Website" multi selector on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Gift Wrapping area.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6278
     */
    public function verificationGiftWrapping()
    {
        $this->navigate('manage_gift_wrapping');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_websites'),
            'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_gift_wrapping'),
            'There is no "Add Gift Wrapping" button on the page');
        $this->clickButton('add_gift_wrapping');
        $this->assertTrue($this->controlIsPresent('multiselect', 'gift_wrapping_websites'),
            'There is no "Website" multi selector on the page');
    }
}
