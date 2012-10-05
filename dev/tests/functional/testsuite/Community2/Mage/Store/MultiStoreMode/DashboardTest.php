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

class Community2_Mage_Store_MultiStoreMode_DashboardTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
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
     * <p>Scope Selector is displayed on the Dashboard page.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there only one Store View - create one more (for enabling Multi Store Mode functionality).</p>
     * <p>4. Navigate to Dashboard page.</p>
     * <p>Expected result:</p>
     * <p>There is "Choose Store View" scope selector on the page.</p>
     *
     * @dataProvider storeModeDataProvider
     * @test
     * @TestlinkId TL-MAGE-6305, TL-MAGE-6307
     * @author Nataliya_Kolenko
     */
    public function verificationDashboardPage($storeMode)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");
        $this->navigate('dashboard');
        //$this->assertTrue($this->isElementPresent($this->_getControlXpath('dropdown', 'store_switcher')));
        $this->assertTrue($this->controlIsPresent('dropdown', 'store_switcher'),
            'There is no "Choose Store View" scope selector on the page');
    }
}
