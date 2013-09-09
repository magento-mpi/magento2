<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Mage_Grid_CustomerStoreBalanceUiTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Assert Pre Conditions</p>
     * <p>1. Log in to backend</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Need to verify that all grid elements are presented on page</p>
     *
     * @test
     */
    public function uiElementsSegmentDetailsGridTest()
    {
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        $searchData = array('email' => $userData['email']);
        $this->customerHelper()->openCustomer($searchData);
        //Data
        $testData = $this->loadDataSet('UiElements', 'edit_customer');
        $this->openTab('store_credit');
        $this->gridHelper()->prepareData($testData);
        //Verification
        $this->assertEmptyVerificationErrors();
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData);
        $expectedHeadersName = $testData['headers'];
        //Verification
        $this->assertEquals($expectedHeadersName, $actualHeadersName,
            "Header names are not equal on customer_segment_report_detail page");
    }
}
