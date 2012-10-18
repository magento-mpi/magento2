<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsSingleStoreMode
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise2_Mage_CmsSingleStoreMode_VerificationCmsAreaSingleStoreModeTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Precondition for test.</p>
     * <p>Navigate to System -> Manage Store.</p>
     * <p>Configure Single-Store Mode.</p>
     */
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $config = $this->loadDataSet('SingleStoreMode', 'enable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>Choose Scope selector does not displayed on the Manage Page Hierarchy page</p>
     * <p>Steps:</p>
     * <p>Navigate to Manage Pages Hierarchy page</p>
     * <p>Expected result:</p>
     * <p>There is no "Choose Scope" selector  on the page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6166
     * author Nataliya_Kolenko
     */
    public function verificationScopeSelector()
    {
        $this->navigate('manage_pages_hierarchy');
        $this->assertFalse($this->controlIsPresent('dropdown', 'choose_scope'),
            'There is "Choose Scope" selector on the page');
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
     * @TestlinkId TL-MAGE-6167
     * author Nataliya_Kolenko
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
     * @TestlinkId TL-MAGE-6168
     * author Nataliya_Kolenko
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
     * @TestlinkId TL-MAGE-6169
     * author Nataliya_Kolenko
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
     * <p>All references to Website-Store-Store View do not displayed in the Banner area</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Banners page</p>
     * <p>2. Click "Add Banner" button</p>
     * <p>3. Choose Content tab</p>
     * <p>4. Click "Back" button</p>
     * <p>Expected result:</p>
     * <p>There is no "Store View Specific Content" fieldset in the Content tab</p>
     * <p>There is no "Visible In" column on the page
     *
     * @test
     * @TestlinkId TL-MAGE-6170
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
        $this->assertFalse($this->controlIsPresent('fieldset', 'store_view_specific_content'),
            'There is "Store View" selector on the page');
        $this->clickButton('back');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_visible_in'),
            'There is "Visible In" dropdown on the page');
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
     * @TestlinkId TL-MAGE-6171
     * author Nataliya_Kolenko
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
}
