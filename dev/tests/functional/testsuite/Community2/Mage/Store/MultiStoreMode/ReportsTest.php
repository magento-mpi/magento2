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

class Community2_Mage_Store_MultiStoreMode_ReportsTest extends Mage_Selenium_TestCase
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
     * Create Store View
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        //Steps
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
    }

    /**
     * <p>Reports</p>
     * <p>Preconditions</p>
     * <p>Magento contain more than one store view</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Go to Reports pages</p>
     * <p>3. Verify that all reports pages contain Scope Selector</p>
     * <p>Expected Result</p>
     * <p>Scope Selector is show on reports pages</p>
     *
     * @dataProvider allReportPagesDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6289
     * @author Maksym_Iakusha
     */
    public function allReportPages($page, $mode)
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure("SingleStoreMode/$mode");
        $this->navigate($page);
        //Validation
        $this->assertTrue($this->controlIsPresent('dropdown', 'store_switcher'),
            "Dropdown associate_to_website present on page");
    }

    public function allReportPagesDataProvider()
    {
        return array(
            array('reports_sales_sales', 'enable_single_store_mode'),
            array('reports_sales_sales', 'disable_single_store_mode'),
            array('report_sales_tax', 'enable_single_store_mode'),
            array('report_sales_tax', 'disable_single_store_mode'),
            array('report_sales_invoiced', 'enable_single_store_mode'),
            array('report_sales_invoiced', 'disable_single_store_mode'),
            array('report_sales_shipping', 'enable_single_store_mode'),
            array('report_sales_shipping', 'disable_single_store_mode'),
            array('report_sales_refunded', 'enable_single_store_mode'),
            array('report_sales_refunded', 'disable_single_store_mode'),
            array('report_sales_coupons', 'enable_single_store_mode'),
            array('report_sales_coupons', 'disable_single_store_mode'),
            array('report_shopcart_abandoned', 'enable_single_store_mode'),
            array('report_shopcart_abandoned', 'disable_single_store_mode'),
            array('report_sales_bestsellers', 'enable_single_store_mode'),
            array('report_sales_bestsellers', 'disable_single_store_mode'),
            array('report_product_sold', 'enable_single_store_mode'),
            array('report_product_sold', 'disable_single_store_mode'),
            array('report_product_viewed', 'enable_single_store_mode'),
            array('report_product_viewed', 'disable_single_store_mode'),
            array('report_product_lowstock', 'enable_single_store_mode'),
            array('report_product_lowstock', 'disable_single_store_mode'),
            array('report_product_downloads', 'enable_single_store_mode'),
            array('report_product_downloads', 'disable_single_store_mode'),
            array('report_customer_accounts', 'enable_single_store_mode'),
            array('report_customer_accounts', 'disable_single_store_mode'),
            array('report_customer_totals', 'enable_single_store_mode'),
            array('report_customer_totals', 'disable_single_store_mode'),
            array('report_customer_orders', 'enable_single_store_mode'),
            array('report_customer_orders', 'disable_single_store_mode'),
            array('report_tag_popular', 'enable_single_store_mode'),
            array('report_tag_popular', 'disable_single_store_mode'),
        );
    }
}
