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
class Core_Mage_Grid_AdminUser_GridTest extends Mage_Selenium_TestCase
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
     * Need to verify that all grid elements are presented on page
     * @test
     * @dataProvider uiElementsTestDataProvider
     */
    public function uiElementsTest($pageName)
    {
        $this->navigate($pageName);
        $testData = $this->loadDataSet('UiElements', $pageName);
        $this->gridHelper()->prepareData($testData);
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Need to verify that all columns in table are presented in the correct order
     * @test
     * @dataProvider uiElementsTestDataProvider
     */
    public function gridHeaderNamesTest($pageName)
    {
        $this->navigate($pageName);
        $testData = $this->loadDataSet('UiElements', $pageName);
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData);
        $expectedHeadersName = $testData['headers'];
        $this->assertEquals($expectedHeadersName, $actualHeadersName, "Header names are not equal on  $pageName page");
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
        );
    }

    /**
     * Need to verify that all ui elements are presented in grid
     * @test
     */
    public function uiElementForRoleUsers()
    {
        $this->navigate('manage_roles');
        $role = array('Administrator');
        $this->adminUserHelper()->openRole($role);
        $this->openTab('role_users');
        $testData = $this->loadDataSet('UiElements', 'role_users');
        $this->gridHelper()->prepareData($testData);
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Need to verify that all ui elements are presented in grid
     * @return array
     * @test
     */
    public function uiElementForUsersRole()
    {
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        unset($testAdminUser['password']);
        unset($testAdminUser['password_confirmation']);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->searchAndOpen($testAdminUser, 'permissionsUserGrid');
        $this->openTab('user_role');
        $testData = $this->loadDataSet('UiElements', 'user_role');
        $this->gridHelper()->prepareData($testData);
        $this->assertEmptyVerificationErrors();

       return $testAdminUser;
    }

    /**
     * Need to verify all header names and order in grid
     *
     * @depends uiElementForUsersRole
     * @param $testAdminUser
     * @test
     */
    public function headersForUserRole($testAdminUser)
    {
        $testData = $this->loadDataSet('UiElements', 'user_role');
        $expectedHeadersName = $testData['headers'];
        $this->navigate('manage_admin_users');
        $this->searchAndOpen($testAdminUser, 'permissionsUserGrid');
        $this->openTab('user_role');
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData);
        $this->assertEquals($expectedHeadersName, $actualHeadersName,
            'Header names in grid Admin User Role is not equals');
    }
}