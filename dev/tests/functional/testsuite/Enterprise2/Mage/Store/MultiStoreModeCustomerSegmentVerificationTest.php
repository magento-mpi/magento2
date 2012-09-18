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

class Enterprise2_Mage_Store_MultiStoreModeCustomerSegmentVerificationTest extends Mage_Selenium_TestCase
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
     * <p>All references to Website-Store-Store View are displayed in the Customer Segments area</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Segments page</p>
     * <p>2. Click "Add Segment" button</p>
     * <p>Expected result:</p>
     * <p>There is "Website" column on the page</p>
     * <p>There is "Assigned to Website" multiselector on the page</p>
     *
     * @param string $singleStoreModeEnabler
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6228
     * @author Nataliya_Kolenko
     */
    public function verificationCustomerSegments($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_customer_segments');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('button', 'add_segment'),
            'There is no "Add Segment" button on the page');
        $this->clickButton('add_segment');
        $this->assertTrue($this->controlIsPresent('multiselect', 'assigned_to_website'),
            'There is no "Assigned to Website" selector on the page');
    }
}
