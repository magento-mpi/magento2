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
    public function setUpBeforeTests()
    {
        $this->fail('check');
        $this->loginAdminUser();
        $this->fail('check');
    }

    /**
     * <p>Precondition method</p>
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
        $loginData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name'],
            'interface_locale' =>'English (United States) / English (United States)'));
        $this->adminUserHelper()->createAdminUser($loginData);
        $this->assertMessagePresent('success', 'success_saved_user');

        return $loginData;
    }

    /**
     * @param $loginData
     */
    protected function _goToMyAccount($loginData)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($loginData);
        //go to My Account link from Account Settings area
        $adminUserLocator = $this->_getControlXpath('link', 'account_avatar');
        $availableElement = $this->elementIsPresent($adminUserLocator);
        if ($availableElement){
            $this->focusOnElement($availableElement);
    $this->clickControl('link', 'account_avatar', false);
    $this->clickControl('link', 'account_settings');
    }
    $this->validatePage('my_account');
    }

    /**
     * <p>Test navigation.</p>
     *
     * @param array $loginData
     * @test
     * @depends createAdminWithTestRole
     */
    public function navigationTest($loginData)
    {
        $this->_goToMyAccount($loginData);
        $this->assertTrue($this->buttonIsPresent('save_account'),
            'There is no "Save account" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset_filter'), 'There is no "Reset" button on the page');;
        $this->assertTrue($this->controlIsPresent('dropdown', 'interface_locale'),
            'There is no "Interface Locale" dropdown on the page');
    }

    /**
     * <p>Need to verify that it is possible change locale using My Account settings</p>
     *
     * @param string $locale
     * @param string $message
     * @param array $loginData
     *
     * @test
     * @depends createAdminWithTestRole
     * @dataProvider changeInterfaceLocaleDataProvider
     * @TestlinkId TL-MAGE-6927
     */
    public function changeInterfaceLocale($locale, $message, $loginData)
    {
        $this->_goToMyAccount($loginData);
        $this->fillDropdown('interface_locale', $locale);
        $this->clickButton('save_account', false);
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
     * <p>Need to verify that it is possible change locale by editing own user</p>
     *
     * @param $locale
     * @param $message
     * @param $loginData
     *
     * @test
     * @depends createAdminWithTestRole
     * @dataProvider changeInterfaceLocalByUserDataProvider
     * @TestlinkId TL-MAGE-6928
     */
    public function changeInterfaceLocalByUser($locale, $message, $loginData)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('manage_admin_users');
        $searchData = $this->loadDataSet('AdminUsers', 'search_admin_user',
            array('email' => $loginData['email'],
                'first_name' => $loginData['first_name'],
                'last_name' => $loginData['last_name'],
                'user_name' => $loginData['user_name']));
        $this->searchAndOpen($searchData, 'permissionsUserGrid');
        $this->fillDropdown('interface_locale', $locale);
        $this->clickButton('save_admin_user');
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

