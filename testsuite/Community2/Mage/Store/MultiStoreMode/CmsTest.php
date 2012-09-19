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

class Community2_Mage_Store_MultiStoreMode_CmsTest extends Mage_Selenium_TestCase
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
     * <p>All references to Website-Store-Store View are displayed in the Manage Content area</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Pages page</p>
     * <p>2. Click "Add Mew Page" button</p>
     * <p>3. Click "Back" button</p>
     * <p>Expected result:</p>
     * <p>There is "Store View" selector on the page</p>
     * <p>There is "Store View" column on the page</p>
     *
     * @dataProvider storeModeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6196
     * @author Nataliya_Kolenko
     */
    public function verificationManageContent($storeMode)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");
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

    public function storeModeDataProvider()
    {
        return array(
            array('enable_single_store_mode'),
            array('disable_single_store_mode')
        );
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
     * @dataProvider storeModeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6197
     * @author Nataliya_Kolenko
     */
    public function verificationStaticBlocks($storeMode)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");
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
            array('cms_page_link', 'enable_single_store_mode'),
            array('cms_page_link', 'disable_single_store_mode'),
            array('cms_static_block', 'enable_single_store_mode'),
            array('cms_static_block', 'disable_single_store_mode'),
            array('catalog_category_link', 'enable_single_store_mode'),
            array('catalog_category_link', 'disable_single_store_mode'),
            array('catalog_new_products_list', 'enable_single_store_mode'),
            array('catalog_new_products_list', 'disable_single_store_mode'),
            array('catalog_product_link', 'enable_single_store_mode'),
            array('catalog_product_link', 'disable_single_store_mode'),
            array('orders_and_returns', 'enable_single_store_mode'),
            array('orders_and_returns', 'disable_single_store_mode'),
            array('recently_compared_products', 'enable_single_store_mode'),
            array('recently_compared_products', 'disable_single_store_mode'),
            array('recently_viewed_products', 'enable_single_store_mode'),
            array('recently_viewed_products', 'disable_single_store_mode'),
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
     * @dataProvider storeModeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6200
     * @author Nataliya_Kolenko
     */
    public function verificationPolls($storeMode)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");
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
