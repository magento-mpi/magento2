<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store_MultiStoreMode
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Community2_Mage_Store_MultiStoreMode_SystemTest extends Mage_Selenium_TestCase
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

    public function storeModeDataProvider()
    {
        return array(
            array('enable_single_store_mode'),
            array('disable_single_store_mode'));
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Schedule Design area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Design page</p>
     * <p>5. Click "Add Design Change" button.</p>
     * <p>Expected result:</p>
     * <p>There is "Store" column on the page.</p>
     * <p>There is "Store" multi selector on the "New Design Change" page.</p>
     *
     * @dataProvider storeModeDataProvider
     * @test
     * @TestlinkId TL-MAGE-6330, TL-MAGE-6331
     * @author Nataliya_Kolenko
     */
    public function verificationDesignSchedule($storeMode)
    {

        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");$this->navigate('system_design');
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
     * @dataProvider storeModeDataProvider
     * @test
     * @TestlinkId TL-MAGE-6332, TL-MAGE-6333
     * @author Nataliya_Kolenko
     */
    public function verificationDesignEditor($storeMode)
    {

        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");$this->navigate('system_design_editor');
        $this->assertTrue($this->controlIsPresent('fieldset', 'context_information'),
            'There is no "Content Information" field set on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Import Export-Dataflow-Profiles area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Profiles page.</p>
     * <p>5. Click "Add New Profile" button.</p>
     * <p>Expected result:</p>
     * <p>There is "Store" column on the page.</p>
     * <p>There is "Store" multi selector on the page.</p>
     *
     * @dataProvider storeModeDataProvider
     * @test
     * @TestlinkId TL-MAGE-6334, TL-MAGE-6335
     * @author Nataliya_Kolenko
     */
    public function verificationImportExportDataflowProfiles($storeMode)
    {

        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");$this->navigate('system_convert_gui');
        $this->assertTrue($this->controlIsPresent('dropdown', 'store'), 'There is no "Store" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_profile'),
            'There is no "Add New Profile" button on the page');
        $this->clickButton('add_new_profile');
        $this->assertTrue($this->controlIsPresent('dropdown', 'store'),
            'There is no "Store" multi selector on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Order Statuses area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Order Statuses page.</p>
     * <p>5. Click "Create New Status" button.</p>
     * <p>Expected result:</p>
     * <p>There is "Store View Specific Labels" field set on the page.</p>
     *
     * @dataProvider storeModeDataProvider
     * @test
     * @TestlinkId TL-MAGE-6336, TL-MAGE-6337
     * @author Nataliya_Kolenko
     */
    public function verificationOrderStatuses($storeMode)
    {

        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");$this->navigate('order_statuses');
        $this->assertTrue($this->controlIsPresent('button', 'create_new_status'),
            'There is no "Create New Status" button on the page');
        $this->clickButton('create_new_status');
        $this->assertTrue($this->controlIsPresent('fieldset', 'store_view_specific_labels'),
            'There is no "Store View Specific Labels" field set on the page');
    }
}
