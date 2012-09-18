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
 */

class Enterprise2_Mage_Store_SingleStoreModeEnabledCustomerSegmentTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Precondition for test</p>
     * <p>Navigate to System -> Manage Store</p>
     * <p>Configure Single-Store Mode</p>
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

    public function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>All references to Website-Store-Store View do not displayed in the Customer Segments area</p>
     * <p>Steps:<p/>
     * <p>1. Navigate to Manage Segments page</p>
     * <p>2. Click "Add Segment" button</p>
     * <p>Expected result:</p>
     * <p>There is no "Website" column on the page</p>
     * <p>There is no "Assigned to Website" multiselect on the page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6226
     * @author Nataliya_Kolenko
     */
    public function verificationCustomerSegments()
    {
        $this->navigate('manage_customer_segments');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            'There is "Website" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_segment'),
            'There is no "Add Segment" button on the page');
        $this->clickButton('add_segment');
        $this->assertFalse($this->controlIsPresent('multiselect', 'assigned_to_website'),
            'There is "Assigned to Website" selector on the page');
    }


}
