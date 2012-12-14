<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Grid_AdminUser
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
     * Method that goes thru test data array and adds Verification Messages
     * @param string $pageName
     */
    protected function _verifyControlsOnGridPage($pageName)
    {
        $page = $this->loadDataSet('Grid', 'grid');
        if (array_key_exists('headers', $page['user_role'])) {
            unset($page[$pageName]['headers']);
        }
        foreach ($page[$pageName] as $control => $type) {
            foreach ($type as $typeName => $name) {
                if (!$this->controlIsPresent($control, $typeName)) {
                    $this->addVerificationMessage("The $control $typeName is not present on page role_users");
                }
            }
        }
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
        $this->_verifyControlsOnGridPage($pageName);
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
        $testData = $this->loadDataSet('Grid', 'grid');
        // 'tablename' value is required for identify grid(table) xPath
        $tableNameValue = array_search('tablename', $testData[$pageName]['fieldset']);
        if ($tableNameValue) {
            $tableXpath = $this->_getControlXpath('fieldset', $tableNameValue);
            $actualHeadersName = $this->getTableHeadRowNames($tableXpath);
        } else {
            $this->fail('Should be at least one key in field section with value "tablename" ');
        }
        $expectedHeadersName = $testData[$pageName]['headers'];
        $this->assertNotNull($expectedHeadersName, 'Array(dataset) with header names is not defined');
        $this->assertEquals($actualHeadersName, $expectedHeadersName);
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
        $this->_verifyControlsOnGridPage('role_users');
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
        $this->_verifyControlsOnGridPage('user_role');
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
        $testData = $this->loadDataSet('Grid', 'grid');
        $tableNameValue = array_search('tablename', $testData['user_role']['fieldset']);
        $expectedHeadersName = $testData['user_role']['headers'];
        $this->navigate('manage_admin_users');
        $this->searchAndOpen($testAdminUser, 'permissionsUserGrid');
        $this->openTab('user_role');
        $tableXpath = $this->_getControlXpath('fieldset', $tableNameValue);
        $actualHeadersName = $this->getTableHeadRowNames($tableXpath);
        $this->assertEquals($actualHeadersName, $expectedHeadersName);
    }
}