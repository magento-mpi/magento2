<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Acl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ACL tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Acl_AdminLanguageSelectionTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->admin('log_in_to_admin');
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * Precondition method
     *
     * @test
     * @return array
     */
    public function createAdminWithTestRole()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $loginData = $this->loadDataSet('AdminUsers', 'generic_admin_user', array(
            'role_name' => $roleSource['role_info_tab']['role_name'],
            'interface_locale' => 'English (United States) / English (United States)'
        ));
        $this->adminUserHelper()->createAdminUser($loginData);
        $this->assertMessagePresent('success', 'success_saved_user');

        return array(
            'loginData' => array('user_name' => $loginData['user_name'], 'password' => $loginData['password']),
            'email' => $loginData['email'],
            'user_name' => $loginData['user_name']
        );
    }

    /**
     * Test navigation
     *
     * @param array $userData
     * @test
     * @depends createAdminWithTestRole
     */
    public function navigationTest($userData)
    {
        $this->adminUserHelper()->loginAdmin($userData['loginData']);
        $this->adminUserHelper()->goToMyAccount();
        $this->assertTrue($this->buttonIsPresent('save_account'), 'There is no "Save account" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset_filter'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'interface_locale'),
            'There is no "Interface Locale" dropdown on the page');
    }

    /**
     * Need to verify that it is possible change locale using My Account settings
     *
     * @param string $locale
     * @param string $message
     * @param array $userData
     *
     * @test
     * @dataProvider changeInterfaceLocaleDataProvider
     * @depends createAdminWithTestRole
     * @TestlinkId TL-MAGE-6927
     */
    public function changeInterfaceLocale($locale, $message, $userData)
    {
        $this->markTestIncomplete('MAGETWO-11335');
        $this->adminUserHelper()->loginAdmin($userData['loginData']);
        $this->adminUserHelper()->goToMyAccount();
        $this->fillDropdown('interface_locale', $locale);
        $this->saveForm('save_account');
        $this->assertMessagePresent('success', $message);
    }

    public function changeInterfaceLocaleDataProvider()
    {
        return array(
            array('Deutsch (Deutschland) / German (Germany)', 'success_german'),
            array('English (United States) / Englisch (Vereinigte Staaten)', 'success_account_saved'),
        );
    }

    /**
     * Need to verify that it is possible change locale by editing own user
     *
     * @param $locale
     * @param $message
     * @param $userData
     *
     * @test
     * @dataProvider changeInterfaceLocalByUserDataProvider
     * @depends createAdminWithTestRole
     * @TestlinkId TL-MAGE-6928
     */
    public function changeInterfaceLocalByUser($locale, $message, $userData)
    {
        $this->markTestIncomplete('MAGETWO-11335');
        $this->adminUserHelper()->loginAdmin($userData['loginData']);
        $this->navigate('manage_admin_users');
        $searchData = $this->loadDataSet('AdminUsers', 'search_admin_user', array(
            'email' => $userData['email'],
            'user_name' => $userData['user_name']
        ));
        $this->adminUserHelper()->openAdminUser($searchData);
        $this->fillDropdown('interface_locale', $locale);
        $this->saveForm('save_admin_user');
        $this->assertMessagePresent('success', $message);
    }

    public function changeInterfaceLocalByUserDataProvider()
    {
        return array(
            array('Deutsch (Deutschland) / German (Germany)', 'success_user_saved_german'),
            array('English (United States) / Englisch (Vereinigte Staaten)', 'success_saved_user'),
        );
    }
}
