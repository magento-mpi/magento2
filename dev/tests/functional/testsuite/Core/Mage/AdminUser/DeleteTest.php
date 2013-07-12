<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminUser
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Deleting Admin User
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_AdminUser_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Permissions -> Users.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
    }

    /**
     * <p>Create Admin User (all required fields are filled).</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3149
     */
    public function deleteAdminUserDeletable()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user');
        $searchData = $this->loadDataSet('AdminUsers', 'search_admin_user', array(
            'email' => $userData['email'],
            'user_name' => $userData['user_name']
        ));
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->openAdminUser($searchData);
        //Steps
        $this->clickButtonAndConfirm('delete_user', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_user');
    }

    /**
     * <p>Delete logged in as Admin User</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5228
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function deleteAdminUserCurrent()
    {
        //Data
        $searchData = $this->loadDataSet('AdminUsers', 'search_admin_user');
        $searchCurrentUser = array();
        //Steps
        $this->adminUserHelper()->goToMyAccount();
        foreach ($searchData as $key => $value) {
            $searchCurrentUser[$key] = $this->getControlAttribute('field', $key, 'value');
        }
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->openAdminUser($searchCurrentUser);
        //Verifying
        $this->clickButtonAndConfirm('delete_user', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('error', 'cannot_delete_account');
    }
}