<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftRegistry
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Registry creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Grid_Report_GridTest extends Mage_Selenium_TestCase
{
    /**
     *
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Post conditions:</p>
     * <p>Log out from Backend.</p>
     */
    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * Need to verify that all elements is presented on invitation report_invitations_customers page
     * @test
     * @dataProvider uiElementsTestDataProvider
     *
     */
    public function uiElementsTest($pageName)
    {
        $this->loginAdminUser();
        $this->navigate($pageName);
        $page = $this->loadDataSet('Report', 'grid');
        foreach ($page[$pageName] as $control=> $type) {
            foreach ($type as $typeName=> $name) {
                if (!$this->controlIsPresent($control, $typeName)) {
                    $this->addVerificationMessage("The $control $typeName is not present on page $pageName");
                }
            }

        }
        $this->assertEmptyVerificationErrors();
    }

    public function uiElementsTestDataProvider()
    {
        return array(array('report_invitations_customers'),
                     array('report_product_sold'),
                     array('report_customer_totals'));
    }

    /**
     * Need to verify count of Grid Rows according to "From:", "To:","Show By:" values
     * @test
     *
     * @dataProvider countGridRowsTestDataProvider
     */
    public function countGridRows($page, $gridTableElement, $dataSet)
    {
        $this->navigate($page);
        $data = $this->loadDataSet('Report', $dataSet);
        $this->fillFieldset($data, $page);
        $this->clickButton('refresh');
        $gridXpath = $this->_getControlXpath('pageelement', $gridTableElement);
        $this->assertCount(3, $this->getElementsByXpath($gridXpath . '/tbody/tr'),
            'Wrong records number in grid $gridTableElement ');
    }

    public function countGridRowsTestDataProvider()
    {
        return array(array('report_product_sold', 'product_sold_grid', 'count_rows_by_day'),
                     array('report_product_sold', 'product_sold_grid', 'count_rows_by_month'),
                     array('report_product_sold', 'product_sold_grid', 'count_rows_by_year'),
                     array('report_invitations_customers', 'report_invitations_customers_grid', 'count_rows_by_day'),
                     array('report_invitations_customers', 'report_invitations_customers_grid', 'count_rows_by_month'),
                     array('report_invitations_customers', 'report_invitations_customers_grid', 'count_rows_by_year'),
                     array('report_customer_totals', 'customer_by_orders_total_table', 'count_rows_by_year'));
    }
}