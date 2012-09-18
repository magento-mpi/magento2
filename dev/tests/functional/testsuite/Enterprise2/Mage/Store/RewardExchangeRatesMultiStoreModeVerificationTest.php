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

class Enterprise2_Mage_Store_RewardExchangeRatesMultiStoreModeVerificationTest extends Mage_Selenium_TestCase
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
     * <p>All references to Website-Store-Store View are displayed in the Reward Exchange Rates area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there only one Store View - create one more (for enabling Multi Store Mode functionality).</p>
     * <p>4. Navigate to Manage Reward Exchange Rates page</p>
     * <p>5. Click "Add New Rate" button</p>
     * <p>Expected result:</p>
     * <p>There is "Website" column on the page</p>
     * <p>There is "Website" multi selector on the page</p>
     *
     * @param string $singleStoreModeEnabler
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6235
     * @author Nataliya_Kolenko
     */
    public function verificationRewardExchangeRates($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_reward_rates');
        $this->assertTrue($this->controlIsPresent('dropdown', 'website'),
            'There is no "Website" multi selector on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_new_rate'),
            'There is no "Add New Rate" button on the page');
        $this->clickButton('add_new_rate');
        $this->assertTrue($this->controlIsPresent('dropdown', 'website'), 'There is no "Website" selector on the page');
    }
}
