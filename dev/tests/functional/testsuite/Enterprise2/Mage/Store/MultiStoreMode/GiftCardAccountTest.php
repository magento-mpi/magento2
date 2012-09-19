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

class Enterprise2_Mage_Store_MultiStoreMode_GiftCardAccountTest extends Mage_Selenium_TestCase
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

    public function singleStoreModeEnablerDataProvider()
    {
        return array(
            array('enable_single_store_mode'),
            array('disable_single_store_mode'));
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Gift Card Account area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there only one Store View - create one more (for enabling Multi Store Mode functionality).</p>
     * <p>4. Navigate to Manage Gift Card Accounts page.</p>
     * <p>5. Click "Add Gift Card Account" button.</p>
     * <p>6. Navigate to Send Gift Cart tab.</p>
     * <p>Expected result:</p>
     * <p>There is "Website" column on the page.</p>
     * <p>There is "Website" multi selector on the page.</p>
     * <p>There is "Send Email from the Following Store View" multi selector in the tab.</p>
     *
     * @param string $singleStoreModeEnabler
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6237
     * @author Nataliya_Kolenko
     */
    public function verificationGiftCardAccounts($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
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
