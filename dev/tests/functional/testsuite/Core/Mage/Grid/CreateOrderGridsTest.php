<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Grid
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Verification Customer grid during order creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Grid_CreateOrderGridsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Assert Pre Conditions</p>
     * <p>1. Log in to backend</p>
     * <p>2. Navigate to Sales-Orders</p>
     * <p>3. Click Create New Order Button</p>     *
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        //Steps
        $this->navigate('manage_sales_orders');
        $this->clickButton('create_new_order');
    }

    /**
     * <p>Post conditions:</p>
     * <p>Log out from Backend.</p>
     */
    protected function tearDownAfterTestClass()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Need to verify that all grid elements are presented on page</p>
     *
     * @test
     */
    public function uiElementsCustomerGridTest()
    {
        //Data
        $testData = $this->loadDataSet('UiElements', 'create_order_for_new_customer');
        $this->gridHelper()->prepareData($testData);
        //Verification
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Need to verify that all columns in table are presented in the correct order</p>
     *
     * @test
     */
    public function gridHeaderNamesTest()
    {
        //Data
        $testData = $this->loadDataSet('UiElements', 'create_order_for_new_customer');
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData);
        $expectedHeadersName = $testData['headers'];
        //Verification
        $this->assertEquals($expectedHeadersName, $actualHeadersName,
            "Header names are not equal on create_order_for_new_customer page");
    }
}
