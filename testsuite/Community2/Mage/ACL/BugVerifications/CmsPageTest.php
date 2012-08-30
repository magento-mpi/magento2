<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ACL
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Community2_Mage_ACL_BugVerifications_CmsPageTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Bug Cover<p/>
     * <p>Verification of MAGETWO-2578:</p>
     * <p>"Save Page" and "Save and Continue Edit" buttons are not presented for admin role with full Custom Resources</p>
     *
     * <p>Preconditions:</p>
     * <p>1. Create test role:</p>
     * <p>On Role Resource tab:</p>
     * <p>"Resource Access" drop-down = "Custom"</p>
     * <p>"Resources" = check all exist cheсk-boxes (custom full admin resources)</p>
     * <p>2. Create Test Admin User with test role.(System>permissions>Users)</p>
     * <p>Steps:</p>
     * <p>1. Login to backend as Test Admin User</p>
     * <p>2. Go to CMS-Page (in EE CMS-Page-Manage Content)</p>
     * <p>3. Click "Add New Page" button</p>
     * <p>Expected results:</p>
     * <p> Buttons : 'Back', 'Reset', 'Save Page', 'Save And Continue Edit' are presented on the page </p>
     *
     * @test
     * @TestlinkId TL-MAGE-6080
     */
    public function CmsPageButton()
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('CmsPage', 'generic_admin_user_role_for_bug');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //Create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('manage_cms_pages');
        $this->clickButton('add_new_page');
        $buttonsTrue = array('back', 'reset', 'save_page', 'save_and_continue_edit');
        foreach ($buttonsTrue as $button) {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page, should be presented");
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}