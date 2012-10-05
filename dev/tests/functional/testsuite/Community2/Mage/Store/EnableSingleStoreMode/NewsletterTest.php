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

class Community2_Mage_Store_EnableSingleStoreMode_NewsletterTest extends Mage_Selenium_TestCase
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

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }

    /**
     * <p>All references to Website-Store-Store View are not displayed in the Newsletter Subscribers area.</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend.</p>
     * <p>2. Navigate to System - Manage Stores.</p>
     * <p>3. If there more one Store View - delete except Default Store View.</p>
     * <p>4. Navigate to Newsletter Subscribers page.</p>
     * <p>Expected result:</p>
     * <p>There is no "Website" multi selector on the page.</p>
     * <p>There is no "Store" multi selector on the page.</p>
     * <p>There is no "Store View" multi selector on the page.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6311
     * @author Nataliya_Kolenko
     */
    public function verificationNewsletterSubscribers()
    {
        $this->navigate('newsletter_subscribers');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_website'),
            'There is no "Website" scope selector on the page');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store'),
            'There is no "Store" scope selector on the page');
        $this->assertFalse($this->controlIsPresent('dropdown', 'filter_store_view'),
            'There is no "Store View" scope selector on the page');
    }
}