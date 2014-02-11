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
class Enterprise_Mage_Grid_CatalogSearchEditUiTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Assert Pre Conditions</p>
     * <p>1. Log in to backend</p>
     * <p>2. Navigate to Marketing-Search Terms</p>
     * <p>3. Click "+" button for add new search term</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        //Steps
        $this->navigate('search_terms');
        $this->clickButton('add_new_search_term');
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
        $testData = $this->loadDataSet('UiElements', 'catalog_search_edit_page');
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
        $testData = $this->loadDataSet('UiElements', 'catalog_search_edit_page');
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData);
        $expectedHeadersName = $testData['headers'];
        //Verification
        $this->assertEquals($expectedHeadersName, $actualHeadersName,
            "Header names are not equal on catalog_search_edit_page page");
    }
}
