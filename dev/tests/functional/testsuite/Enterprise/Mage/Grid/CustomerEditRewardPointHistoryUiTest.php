<?php
    /**
     * {license_notice}
     *
     * @category    Magento
     * @package     Enterprise_Grid
     * @subpackage  functional_tests
     * @copyright   {copyright}
     * @license     {license_link}
     */
class Enterprise_Mage_Grid_CustomerEditRewardPointHistoryUiTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Pre Conditions</p>
     * <p>1. Log in admin user</p>
     * <p>2. Navigate to Customers-All Customers page</p>
     * <p>3. Create new customer for test</p>
     * <p>4. Find test customer in grid and open</p>
     * <p>5. Click 'Reward Point' tab</p>
     * <p>6. Open Reward Points History block</p>
     *
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $searchData = $this->loadDataSet('Customers', 'search_customer', array('email' => $userData['email']));
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer($searchData);
        $this->openTab('reward_points');
        $this->clickControl('link', 'reward_points_history_link', false);
        $this->waitForAjax();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Need to verify that all grid elements are presented on page</p>
     *
     * @test
     */
    public function uiElementsGridTest()
    {
        //Data
        $testData = $this->loadDataSet('UiElements', 'reward_points_history_grid');
        $this->gridHelper()->prepareData($testData);
        //Verification
        $this->assertEmptyVerificationErrors();
    }

    /**
     *
     * <p>Need to verify that all columns in table are presented in the correct order</p>
     *
     * @test
     */
    public function gridHeaderNamesTest()
    {
        //Data
        $testData = $this->loadDataSet('UiElements', 'reward_points_history_grid');
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData);
        $expectedHeadersName = $testData['headers'];
        //Verification
        $this->assertEquals($expectedHeadersName, $actualHeadersName,
            "Header names are not equal for reward_points_history_grid on customer edit page");
    }
}