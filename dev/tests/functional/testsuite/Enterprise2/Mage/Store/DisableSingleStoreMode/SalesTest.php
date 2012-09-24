<?php
/**
* Magento
*
* {license_notice}
*
* @category    Magento
* @package     Mage_Store_DisableSingleStoreMode
* @subpackage  functional_tests
* @copyright   {copyright}
* @license     {license_link}
*/

class Enterprise2_Mage_Store_DisableSingleStoreMode_SalesTest extends Mage_Selenium_TestCase
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
     * <p>All references to Website-Store-Store View are displayed in the Gift Wrapping area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - No.</p>
     * <p>6. Navigate to Manage Gift Wrapping page.</p>
     * <p>7. Click "Add Gift Wrapping" button.</p>
     * <p>Expected result:</p>
     * <p>There is "Websites" column on the page.</p>
     * <p>There is "Websites" multi selector on the page.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6278
     * @author Nataliya_Kolenko
     */
    public function verificationGiftWrapping()
    {
        $this->navigate('manage_gift_wrapping');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_websites'),
            'There is no "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_gift_wrapping'),
            'There is no "Add Gift Wrapping" button on the page');
        $this->clickButton('add_gift_wrapping');
        $this->assertTrue($this->controlIsPresent('multiselect', 'gift_wrapping_websites'),
            'There is no "Website" multi selector on the page');
    }
}
