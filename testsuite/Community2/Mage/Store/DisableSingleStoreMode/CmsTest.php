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

class Community2_Mage_Store_DisableSingleStoreMode_CmsTest extends Mage_Selenium_TestCase
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
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
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
     * @TestlinkId TL-MAGE-6218
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
     * @test
     * @TestlinkId TL-MAGE-6219
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
     *
     * @test
     * @TestlinkId TL-MAGE-6220
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
     * @TestlinkId TL-MAGE-6222
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
