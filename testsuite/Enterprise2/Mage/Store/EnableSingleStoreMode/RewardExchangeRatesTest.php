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

class Enterprise2_Mage_Store_EnableSingleStoreMode_RewardExchangeRatesTest extends Mage_Selenium_TestCase
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
     * <p>All references to Website-Store-Store View do not displayed in the Reward Exchange Rates area</p>
     * <p>Steps:<p/>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate System Configuration - Single Store Mode.</p>
     * <p>5. Configure Enable Single-Store Mode - Yes.</p>
     * <p>6. Navigate to Manage Reward Exchange Rates page.</p>
     * <p>7. Click "Add New Rate" button.</p>
     * <p>Expected result:</p>
     * <p>There is no "Website" column on the page.</p>
     * <p>There is no "Website" multi selector on the page.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6233
     * @author Nataliya_Kolenko
     */
    public function verificationRewardExchangeRates()
    {
        $this->navigate('manage_reward_rates');
        $this->assertFalse($this->controlIsPresent('dropdown', 'website'), 'There is "Website" column on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rate'),
            'There is no "Add New Rate" button on the page');
        $this->clickButton('add_new_rate');
        $this->assertFalse($this->controlIsPresent('dropdown', 'website'),
            'There is "Website" multi selector on the page');
    }
}
