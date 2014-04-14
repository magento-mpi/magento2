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
        $this->markTestIncomplete('MAGETWO-11335');
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
        $role = 'Administrators';
        $user1 = $this->loadDataSet('AdminUsers', 'generic_admin_user', array('role_name' => $role));
        $user2 = $this->loadDataSet('AdminUsers', 'generic_admin_user', array('role_name' => $role));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($user1);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($user2);
        $this->assertMessagePresent('success', 'success_saved_user');

        return array(
            'login1' => array('user_name' => $user1['user_name'], 'password' => $user1['password']),
            'login2' => array('user_name' => $user2['user_name'], 'password' => $user2['password']),
            'search' => array('email' => $user2['email'], 'user_name' => $user2['user_name'])
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
        $this->adminUserHelper()->loginAdmin($userData['login1']);
        $this->adminUserHelper()->goToMyAccount();
        $this->assertTrue($this->buttonIsPresent('save_account'), 'There is no "Save account" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset_filter'), 'There is no "Reset" button on the page');
        $this->assertTrue(
            $this->controlIsPresent('dropdown', 'interface_locale'),
            'There is no "Interface Locale" dropdown on the page'
        );
    }

    /**
     * Need to verify that it is possible change locale using My Account settings
     *
     * @param string $locale
     * @param string $message
     * @param string $title
     * @param array $userData
     *
     * @test
     * @dataProvider changeInterfaceLocaleDataProvider
     * @depends createAdminWithTestRole
     * @TestlinkId TL-MAGE-6927
     */
    public function changeInterfaceLocale($locale, $message, $title,$userData)
    {
        $this->adminUserHelper()->loginAdmin($userData['login1']);
        $this->clickControl('link', 'account_avatar', false);
        $this->clickControl('link', 'account_settings', false);
        $this->assertTrue($this->checkCurrentPage('my_account'), $this->getParsedMessages());
        $this->setCurrentPage('my_account');
        $this->fillDropdown('interface_locale', $locale);
        $this->saveForm('save_account', false);
        $this->assertTrue($this->checkCurrentPage('my_account'), $this->getParsedMessages());
        $this->setCurrentPage('my_account');
        $this->assertSame($title, $this->title());
        $this->assertMessagePresent('success', $message);
    }

    public function changeInterfaceLocaleDataProvider()
    {
        return array(
            array(
                'Deutsch (Deutschland) / German (Germany)',
                'success_saved_german_account',
                'Mein Konto / Magento Admin'
            ),
            array(
                'English (United States) / Englisch (Vereinigte Staaten)',
                'success_saved_account',
                'My Account / Magento Admin'
            ),
        );
    }

    /**
     * Need to verify that it is possible change locale by editing own user
     *
     * @param string $locale
     * @param string $message
     * @param string $title
     * @param array $userData
     *
     * @test
     * @dataProvider changeInterfaceLocalByUserDataProvider
     * @depends createAdminWithTestRole
     * @TestlinkId TL-MAGE-6928
     */
    public function changeInterfaceLocalByUser($locale, $message, $title,$userData)
    {
        $searchData = $this->loadDataSet('AdminUsers', 'search_admin_user', $userData['search']);

        $this->adminUserHelper()->loginAdmin($userData['login2']);
        $this->navigate('manage_admin_users', false);
        $this->assertTrue($this->checkCurrentPage('manage_admin_users'), $this->getParsedMessages());
        $this->adminUserHelper()->openAdminUser($searchData);
        $this->fillDropdown('interface_locale', $locale);
        $this->saveForm('save_admin_user', false);
        $this->assertTrue($this->checkCurrentPage('manage_admin_users'), $this->getParsedMessages());
        $this->setCurrentPage('manage_admin_users');
        $this->assertSame($title, $this->title());
        $this->assertMessagePresent('success', $message);
    }

    public function changeInterfaceLocalByUserDataProvider()
    {
        return array(
            array(
                'Deutsch (Deutschland) / German (Germany)',
                'success_saved_german_user',
                'Benutzer / Benutzerrechte / System / Magento Admin'
            ),
            array(
                'English (United States) / Englisch (Vereinigte Staaten)',
                'success_saved_user',
                'Users / Permissions / System / Magento Admin'
            ),
        );
    }
}
