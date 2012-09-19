<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DisableSingleStoreMode
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise2_Mage_Store_DisabledSingleStoreMode_CmsTest extends Mage_Selenium_TestCase
{

    protected function assertPreconditions()
{
    $this->loginAdminUser();
    $this->admin('manage_stores');
    $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
    $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
    $this->navigate('system_configuration');
    $this->systemConfigurationHelper()->configure($config);
}

    /**
     * <p>Choose Scope selector is displayed on the Manage Page Hierarchy page</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Pages Hierarchy page</p>
     * <p>Expected result:</p>
     * <p>There is "Choose Scope" selector  on the page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6217
     * @author Nataliya_Kolenko
     */
    public function verificationManageHierarchy()
    {
        $this->navigate('manage_pages_hierarchy');
        $this->assertTrue($this->controlIsPresent('dropdown', 'choose_scope'),
            'There is no "Choose Scope" selector on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Widget area</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Widget Instances page</p>
     * <p>2. Click "Add New Widget Instance" button</p>
     * <p>3. Fill Settings fields</p>
     * <p>4. Click "Continue"</p>
     * <p>Expected result:</p>
     * <p>There is "Assign to Store Views" selector in the Frontend Properties tab</p>
     *
     * @param string $dataWidgetType
     *
     * @dataProvider widgetTypesDataProvider
     *
     * @test
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
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Banners page</p>
     * <p>2. Click "Add Banner" button</p>
     * <p>3. Choose Content tab</p>
     * <p>4. Click "Back" button
     * <p>Expected result:</p>
     * <p>There is "Store View Specific Content" fieldset in the Content tab</p>
     * <p>There is "Visible In" column on the page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6221
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
        $this->assertTrue($this->controlIsPresent('fieldset', 'specific_content'),
            'There is "Store View Specific Content" selector on the page');
        $this->clickButton('back');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_visible_in'),
            'There is "Visible In" dropdown on the page');
    }
}
