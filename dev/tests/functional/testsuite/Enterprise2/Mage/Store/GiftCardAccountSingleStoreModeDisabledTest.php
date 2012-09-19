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

class Enterprise2_Mage_Store_GiftCardAccountSingleStoreModeDisabledTest extends Mage_Selenium_TestCase
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
     * <p>All references to Website-Store-Store View are displayed in the Gift Card Account area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - No.</p>
     * <p>6. Navigate to Manage Gift Card Accounts page.</p>
     * <p>7. Click "Add Gift Card Account" button.</p>
     * <p>8. Navigate to Send Gift Cart tab.</p>
     * <p>Expected result:</p>
     * <p>There is "Website" column on the page.</p>
     * <p>There is "Website" multi selector on the page.</p>
     * <p>There is "Send Email from the Following Store View" multi selector in the tab.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6239
     * @author Nataliya_Kolenko
     */
    public function verificationGiftCardAccounts()
    {
        $this->navigate('manage_gift_card_account');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" multi selector on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_gift_card_account'),
            'There is no "Add Gift Card Account" button on the page');
        $this->clickButton('add_gift_card_account');
        $this->assertTrue($this->controlIsPresent('multiselect', 'website'),
            'There is no "Website" multi selector on the page');
        $this->assertTrue($this->controlIsPresent('tab', 'send_gift_card'),
            'There is no Send Gift Card tab on the page');
        $this->openTab('send_gift_card');
        $this->assertTrue($this->controlIsPresent('multiselect', 'send_email_from'),
            'There is no Send Email from the Following Store View multi selector on the page');
    }
}
