<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store_EnableSingleStoreMode
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Community2_Mage_Store_EnableSingleStoreMode_SystemTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $config = $this->loadDataSet('SingleStoreMode', 'enable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }
    /**
     * <p>All references to Website-Store-Store View are not displayed in the Schedule Design area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Design page.</p>
     * <p>5. Click "Add Design Change" button.</p>
     * <p>Expected result:</p>
     * <p>There is no "Store" column on the page.</p>
     * <p>There is no "Store" multi selector on the "New Design Change" page.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6321
     * @author Nataliya_Kolenko
     */
    public function verificationDesignSchedule()
    {
        $this->navigate('system_design');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'), 'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_design_change'),
            'There is no "Add Design Change" button on the page');
        $this->clickButton('add_design_change');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'),
            'There is no "Store" multi selector on the page');
    }

    /**
     * <p>"Content Information" field set is not displayed in the Design-Editor area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Visual Design Editor page.</p>
     * <p>Expected result:</p>
     * <p>There is no "Content Information" field set on the page.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6322
     * @author Nataliya_Kolenko
     */
    public function verificationDesignEditor()
    {
        $this->navigate('system_design_editor');
        $this->assertFalse($this->controlIsPresent('fieldset', 'context_information'),
            'There is no "Content Information" field set on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Import Export-Dataflow-Profiles area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Profiles page.</p>
     * <p>5. Click "Add New Profile" button.</p>
     * <p>Expected result:</p>
     * <p>There is no "Store" column on the page.</p>
     * <p>There is no "Store" multi selector on the page.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6323
     * @author Nataliya_Kolenko
     */
    public function verificationImportExportDataflowProfiles()
    {
        $this->navigate('system_convert_gui');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'), 'There is no "Store" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_profile'),
            'There is no "Add New Profile" button on the page');
        $this->clickButton('add_new_profile');
        $this->assertFalse($this->controlIsPresent('dropdown', 'store'),
            'There is no "Store" multi selector on the page');
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Order Statuses area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Order Statuses page.</p>
     * <p>5. Click "Create New Status" button.</p>
     * <p>Expected result:</p>
     * <p>There is no "Store View Specific Labels" field set on the page.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6324
     * @author Nataliya_Kolenko
     */
    public function verificationOrderStatuses()
    {
        $this->navigate('order_statuses');
        $this->assertTrue($this->controlIsPresent('button', 'create_new_status'),
            'There is no "Create New Status" button on the page');
        $this->clickButton('create_new_status');
        $this->assertFalse($this->controlIsPresent('fieldset', 'store_view_specific_labels'),
            'There is no "Store View Specific Labels" field set on the page');
    }
}
