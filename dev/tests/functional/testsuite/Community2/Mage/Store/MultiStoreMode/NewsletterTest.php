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

class Community2_Mage_Store_MultiStoreMode_NewsletterTest extends Mage_Selenium_TestCase
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

    public function storeModeDataProvider()
    {
        return array(
            array('enable_single_store_mode'),
            array('disable_single_store_mode'));
    }

    /**
     * <p>All references to Website-Store-Store View are displayed in the Newsletter Subscribers area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there only one Store View - create one more (for enabling Multi Store Mode functionality).</p>
     * <p>4. Navigate to Newsletter Subscribers page.</p>
     * <p>Expected result:</p>
     * <p>There is "Website" multi selector on the page.</p>
     * <p>There is "Store" multi selector on the page.</p>
     * <p>There is "Store View" multi selector on the page.</p>
     *
     * @dataProvider storeModeDataProvider
     * @test
     * @TestlinkId TL-MAGE-6308, TL-MAGE-6309
     * @author Nataliya_Kolenko
     */
    public function verificationNewsletterSubscribers($storeMode)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$storeMode");
        $this->navigate('newsletter_subscribers');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" scope selector on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store'),
            'There is no "Store" scope selector on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is no "Store View" scope selector on the page');
    }
}