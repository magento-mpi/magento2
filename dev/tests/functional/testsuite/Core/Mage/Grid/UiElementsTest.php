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
 * Verification grids into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Grid_UiElementsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
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
     * @param string $pageName
     * @test
     * @dataProvider uiElementsTestDataProvider
     */
    public function uiElementsTest($pageName)
    {
        //Data
        $testData = $this->loadDataSet('UiElements', $pageName);
        //Steps
        $this->navigate($pageName);
        $this->gridHelper()->prepareData($testData);
        //Verification
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Need to verify that all columns in table are presented in the correct order</p>
     *
     * @param string $pageName
     * @test
     * @dataProvider uiElementsTestDataProvider
     */
    public function gridHeaderNamesTest($pageName)
    {
        //Data
        $testData = $this->loadDataSet('UiElements', $pageName);
        //Steps
        $this->navigate($pageName);
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData);
        $expectedHeadersName = $testData['headers'];
        //Verification
        $this->assertEquals($expectedHeadersName, $actualHeadersName, "Header names are not equal on $pageName page");
    }

    public function uiElementsTestDataProvider()
    {
        return array(
            array('manage_admin_users'),
            array('manage_roles'),
            array('system_email_template'),
            array('system_design'),
            array('xml_sitemap'),
            array('url_rewrite_management'),
            array('manage_attribute_sets'),
            array('search_terms'),
            array('newsletter_problem_reports'),
            array('system_backup'),
            array('manage_tax_zones_and_rates'),
            array('system_custom_variables'),
            array('report_review_customer'),
            array('manage_stores'),
            array('report_review_product'),
            array('report_statistics'),
            array('newsletter_queue'),
            array('theme_list'),
            array('google_content_manage_attributes'),
            array('report_tag_product'),
            array('report_tag_customer'),
            array('report_search'),
            array('manage_ratings'),
            array('manage_cms_widgets'),
            array('paypal_reports'),
            array('api_soap_users'),
            array('manage_customer_groups'),
            array('cache_storage_management'),
            array('order_statuses'),
        );
    }

    /**
     * <p>Need to verify that all ui elements are presented in grid</p>
     *
     * @test
     */
    public function uiElementForRoleUsers()
    {
        //Data
        $testData = $this->loadDataSet('UiElements', 'role_users');
        //Steps
        $this->navigate('manage_roles');
        $role = array('Administrator');
        $this->adminUserHelper()->openRole($role);
        $this->openTab('role_users');
        $this->gridHelper()->prepareData($testData);
        //Verification
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Need to verify that all ui elements are presented in grid</p>
     *
     * @return array
     * @test
     */
    public function uiElementForUsersRole()
    {
        //Precondition - create new test admin users
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user');
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        unset($testAdminUser['password']);
        unset($testAdminUser['password_confirmation']);
        $this->assertMessagePresent('success', 'success_saved_user');
        //Data
        $testData = $this->loadDataSet('UiElements', 'user_role');
        //Steps
        $this->searchAndOpen($testAdminUser, 'permissionsUserGrid');
        $this->openTab('user_role');
        $this->gridHelper()->prepareData($testData);
        //Verification
        $this->assertEmptyVerificationErrors();

        return $testAdminUser;
    }

    /**
     * <p>Need to verify all header names and order in grid</p>
     *
     * @depends uiElementForUsersRole
     * @param array $testAdminUser
     * @test
     */
    public function headersForUserRole($testAdminUser)
    {
        //Data
        $testData = $this->loadDataSet('UiElements', 'user_role');
        $expectedHeadersName = $testData['headers'];
        //Steps
        $this->navigate('manage_admin_users');
        $this->searchAndOpen($testAdminUser, 'permissionsUserGrid');
        $this->openTab('user_role');
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData);
        //Verifications
        $this->assertEquals($expectedHeadersName, $actualHeadersName,
            'Header names in grid Admin User Role is not equals');
    }
}
