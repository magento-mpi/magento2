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

class Enterprise2_Mage_Store_MultiStoreMode_CmsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'manage_stores');
        $qtyElementsInTable = $this->_getControlXpath('pageelement', 'qtyElementsInTable');
        $foundItems = $this->getText($fieldsetXpath . $qtyElementsInTable);
        if ($foundItems == 1) {
            $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
            $this->storeHelper()->createStore($storeViewData, 'store_view');
        }
    }

    /**
     * <p>Choose Scope selector is displayed on the Manage Page Hierarchy page</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Pages Hierarchy page</p>
     * <p>Expected result:</p>
     * <p>There is "Choose Scope" selector  on the page</p>
     *
     * @dataProvider storeModeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6195
     * @author Nataliya_Kolenko
     */
    public function verificationManageHierarchy($storeMode)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");
        $this->navigate('manage_pages_hierarchy');
        $this->assertTrue($this->controlIsPresent('dropdown', 'choose_scope'),
            'There is no "Choose Scope" selector on the page');
    }

    public function storeModeDataProvider()
    {
        return array(
            array('enable_single_store_mode'),
            array('disable_single_store_mode')
        );
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
     * @dataProvider widgetTypesDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6198
     * @author Nataliya_Kolenko
     */
    public function verificationAllTypesOfWidgetsInSingleStoreMode($widgetType, $storeMode)
    {
        $widgetData = $this->loadDataSet('CmsWidget', $widgetType . '_settings');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");
        $this->navigate('manage_cms_widgets');
        $this->clickButton('add_new_widget_instance');
        $this->cmsWidgetsHelper()->fillWidgetSettings($widgetData['settings']);
        $this->assertTrue($this->controlIsPresent('multiselect', 'assign_to_store_views'),
            'There is no "Store View" selector on the page');
    }

    public function widgetTypesDataProvider()
    {
        return array(
            array('banner_rotator', 'enable_single_store_mode'),
            array('banner_rotator', 'disable_single_store_mode'),
            array('cms_hierarchy_node_link', 'enable_single_store_mode'),
            array('cms_hierarchy_node_link', 'disable_single_store_mode'),
            array('catalog_events_carousel', 'enable_single_store_mode'),
            array('catalog_events_carousel', 'disable_single_store_mode'),
            array('gift_registry_search', 'enable_single_store_mode'),
            array('gift_registry_search', 'disable_single_store_mode'),
            array('order_by_sku', 'enable_single_store_mode'),
            array('order_by_sku', 'disable_single_store_mode'),
            array('wishlist_search', 'enable_single_store_mode'),
            array('wishlist_search', 'disable_single_store_mode'),
        );
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Banner area</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Banners page</p>
     * <p>2. Click "Add Banner" button</p>
     * <p>3. Choose Content tab</p>
     * <p>4. Click "Back" button</p>
     * <p>Expected result:</p>
     * <p>There is "Store View Specific Content" fieldset in the Content tab</p>
     * <p>There is "Visible In" column on the page</p>
     *
     * @dataProvider storeModeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6199
     * @author Nataliya_Kolenko
     */
    public function verificationBanners($storeMode)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");
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
}
