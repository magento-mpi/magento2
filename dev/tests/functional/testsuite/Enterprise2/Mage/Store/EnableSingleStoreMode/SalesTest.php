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

class Enterprise2_Mage_Store_EnableSingleStoreMode_SalesTest extends Mage_Selenium_TestCase
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

    public function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Gift Wrapping area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p>
     * <p>6. Navigate to Manage Gift Wrapping page.</p>
     * <p>7. Click "Add Gift Wrapping" button.</p>
     * <p>Expected result:</p>
     * <p>There is no "Websites" column on the page.</p>
     * <p>There is no "Websites" multi selector on the page.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6274
     * @author Nataliya_Kolenko
     */
    public function verificationGiftWrapping()
    {
        $this->navigate('manage_gift_wrapping');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_websites'),
            'There is "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_gift_wrapping'),
            'There is no "Add Gift Wrapping" button on the page');
        $this->clickButton('add_gift_wrapping');
        $this->assertFalse($this->controlIsPresent('multiselect', 'gift_wrapping_websites'),
            'There is "Website" multi selector on the page');
    }
}
