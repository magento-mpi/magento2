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

class Enterprise2_Mage_Store_MultiStoreMode_SalesTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }

    /**
     * Create customer
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');

        return $userData;
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Gift Wrapping area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there only one Store View - create one more (for enabling Multi Store Mode functionality).</p>
     * <p>4. Navigate to Manage Gift Wrapping page.</p>
     * <p>5. Click "Add Gift Wrapping" button.</p>
     * <p>Expected result:</p>
     * <p>There is "Websites" column on the page.</p>
     * <p>There is "Websites" multi selector on the page.</p>
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6282, TL-MAGE-6286
     * @author Nataliya_Kolenko
     */
    public function verificationGiftWrapping($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
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
