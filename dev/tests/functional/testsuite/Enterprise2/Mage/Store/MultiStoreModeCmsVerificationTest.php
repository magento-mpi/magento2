<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsMultiStoreMode
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise2_Mage_Store_CmsVerificationMultiStoreModeTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Precondition for test. Create Store View</p>
     * <p>Steps:</p>
     * <p>1. Click 'Create Store View' button.</p>
     * <p>2. Fill in required fields.</p>
     * <p>3. Click 'Save Store View' button.</p>
     * <p>Expected result:</p>
     * <p>Store View is created.</p>
     * <p>Success Message is displayed</p>     *
     *
     * @test
     * @TestlinkId TL-MAGE-3628
     */
    public function createStoreView()
    {
        $this->navigate('manage_stores');
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
    }

    /**
     * <p>Choose Scope selector is displayed on the Manage Page Hierarchy page</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Pages Hierarchy page</p>
     * <p>Expected result:</p>
     * <p>There is "Choose Scope" selector  on the page</p>
     *
     * @depends createStoreView
     * @test
     * @TestlinkId TL-MAGE-6195
     * author Nataliya_Kolenko
     */
    public function verificationScopeSelector()
    {
        $this->navigate('manage_pages_hierarchy');
        $this->assertTrue($this->controlIsPresent('dropdown', 'choose_scope'),
            'There is no "Choose Scope" selector on the page');
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
     * @depends createStoreView
     * @test
     * @TestlinkId TL-MAGE-6196
     * author Nataliya_Kolenko
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
     * @depends createStoreView
     * @test
     * @TestlinkId TL-MAGE-6197
     * author Nataliya_Kolenko
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
     * @depends createStoreView
     * @test
     * @TestlinkId TL-MAGE-6198
     * author Nataliya_Kolenko
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
            array('cms_page_link'),
            array('cms_static_block'),
            array('catalog_category_link'),
            array('catalog_events_carousel'),
            array('catalog_new_products_list'),
            array('catalog_product_link'),
            array('gift_registry_search'),
            array('order_by_sku'),
            array('orders_and_returns'),
            array('recently_compared_products'),
            array('recently_viewed_products'),
            array('wishlist_search'),
        );
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Banner area</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Banners page</p>
     * <p>2. Click "Add Banner" button</p>
     * <p>3. Choose Content tab</p>
     * <p>Expected result:</p>
     * <p>There is "Store View Specific Content" fieldset in the Content tab</p>
     *
     * @depends createStoreView
     * @test
     * @TestlinkId TL-MAGE-6199
     * author Nataliya_Kolenko
     */
    public function verificationBanners()
    {
        $this->navigate('manage_banners');
        $this->assertTrue($this->controlIsPresent('button', 'add_banner'),
            'There is no "Add Banner" button on the page');
        $this->clickButton('add_banner');
        $this->assertTrue($this->controlIsPresent('tab', 'content'), 'There is Content tab on the page');
        $this->openTab('content');
        $this->assertTrue($this->controlIsPresent('fieldset', 'store_view_specific_content'),
            'There is no "Store View" fieldset on the page');
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
     * @depends createStoreView
     * @test
     * @TestlinkId TL-MAGE-6200
     * author Nataliya_Kolenko
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
}
