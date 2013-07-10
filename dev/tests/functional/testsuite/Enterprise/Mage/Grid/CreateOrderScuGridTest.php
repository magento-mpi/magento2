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
 * Verification SKU error grid into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Grid_CreateOrderScuGridTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Pre Conditions</p>
     * <p>1. Log in admin user</p>
     * <p>2. Navigate to Sales-Order page</p>
     * <p>3. Click Create New Order button</p>
     * <p>4. Click Create New Customer button</p>
     * <p>5. Click Add Product By SKU button</p>
     * <p>6. Add invalid data </p>
     * <p>7. Click Add to Order button </p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        //Steps
        $this->navigate('manage_sales_orders');
        $this->clickButton('create_new_order');
        $this->clickButton('create_new_customer');
        $this->pleaseWait();
        if (!$this->controlIsPresent('button', 'add_products_by_sku')) {
            $this->clickControl('radiobutton', 'choose_first_store', false);
            $this->pleaseWait();
        }
        $this->clickButton('add_products_by_sku');
        $this->addParameter('number', 1);
        $this->addParameter('rowIndex', 1);
        $this->addParameter('sku', 'failData');
        $this->fillField('sku', 'failData');
        $this->fillField('qty', 1);
        $this->clickButton('submit_sku_form');
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
     * <p>Need to verify that all grid elements are presented on page</p>
     *
     * @test
     */
    public function uiElementsSkuErrorGridTest()
    {
        //Data
        $testData = $this->loadDataSet('UiElements', 'create_order_for_new_customer_sku');
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
        $testData = $this->loadDataSet('UiElements', 'create_order_for_new_customer_sku');
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData);
        $expectedHeadersName = $testData['headers'];
        //Verification
        $this->assertEquals($expectedHeadersName, $actualHeadersName,
            "Header names are not equal on create_order_for_new_customer_sku page");
    }
}
