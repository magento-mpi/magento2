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
     * Need to verify that all grid elements is presented on page
     * @test
     * @dataProvider uiElementsTestDataProvider
     *
     */
    public function uiElementsTest($pageName)
    {
        $this->navigate($pageName);
        $testData = $this->loadDataSet('UiElements', 'grid');
        $this->gridHelper()->prepareData($testData, $pageName);
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Need to verify that all columns in table are present in the correct order
     * @test
     * @dataProvider uiElementsTestDataProvider
     *
     */
    public function gridHeaderNamesTest($pageName)
    {
        $this->navigate($pageName);
        $testData = $this->loadDataSet('UiElements', 'grid');
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData[$pageName]);
        $expectedHeadersName = $this->gridHelper()->prepareData($testData, $pageName);;
        $this->assertNotNull($expectedHeadersName, 'Array(dataset) with header names is not defined');
        $this->assertEquals($expectedHeadersName, $actualHeadersName);
    }

    public function uiElementsTestDataProvider()
    {
        return array(
            array('xml_sitemap'),
            array('system_design'),
            array('manage_roles'),
            array('manage_admin_users'),
            array('system_email_template'),
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
        $testData = $this->loadDataSet('UiElements', 'grid');
        $this->gridHelper()->prepareData($testData, 'role_users');
    }

    /**
     * Need to verify that all ui elements are presented in  grid
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
        $testData = $this->loadDataSet('UiElements', 'grid');
        $this->gridHelper()->prepareData($testData, 'user_role');
        $this->assertEmptyVerificationErrors();

       return $testAdminUser;
    }

    /**
     * Need to verify all header names and order in grid
     *
     * @depends uiElementForUsersRole
     * @param $testAdminUser
     *
     * @test
     */
    public function headersForUserRole($testAdminUser)
    {
        $testData = $this->loadDataSet('UiElements', 'grid');
        $expectedHeadersName = $testData['user_role']['headers'];
        $this->navigate('manage_admin_users');
        $this->searchAndOpen($testAdminUser, 'permissionsUserGrid');
        $this->openTab('user_role');
        $actualHeadersName = $this->gridHelper()->getGridHeaders($testData['user_role']);
        $this->assertEquals($actualHeadersName, $expectedHeadersName);
    }
}