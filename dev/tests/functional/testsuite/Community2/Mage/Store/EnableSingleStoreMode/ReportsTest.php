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

class Community2_Mage_Store_DisableSingleStoreMode_ReportsTest extends Mage_Selenium_TestCase
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
     * Enable Single Store Mode
     *
     * @test
     */
    public function preconditionsForTests()
    {
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/enable_single_store_mode');
    }
    /**
     * <p>Reports</p>
     * <p>Preconditions</p>
     * <p>Magento contain only one store view</p>
     * <p>Disable single store mode System->Configuration->General->General->Single-Store Mode</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Go to Reports pages</p>
     * <p>3. Verify that on all reports pages Scope Selector is missing</p>
     * <p>Expected Result</p>
     * <p>Scope Selector is is missing on reports pages</p>
     *
     * @dataProvider allReportPagesDataProvider
     * @depends preconditionsForTests
     *
     * @test
     * @TestlinkId TL-MAGE-6288
     * @author Maksym_Iakusha
     */
    public function allReportPages($page)
    {
        //Steps
        $this->navigate($page);
        //Validation
        $this->assertFalse($this->controlIsPresent('dropdown', 'store_switcher'),
            "Dropdown associate_to_website present on page");
    }

    public function allReportPagesDataProvider()
    {
        return array(
            array('reports_sales_sales'),
            array('report_sales_tax'),
            array('report_sales_invoiced'),
            array('report_sales_shipping'),
            array('report_sales_refunded'),
            array('report_sales_coupons'),
            array('report_shopcart_abandoned'),
            array('report_sales_bestsellers'),
            array('report_product_sold'),
            array('report_product_viewed'),
            array('report_product_lowstock'),
            array('report_product_downloads'),
            array('report_customer_accounts'),
            array('report_customer_totals'),
            array('report_customer_orders'),
            array('report_tag_popular'),
        );
    }
}
