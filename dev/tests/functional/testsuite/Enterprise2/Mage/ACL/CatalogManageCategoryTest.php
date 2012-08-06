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

class Enterprise2_Mage_ACL_CatalogManageCategoryTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Post conditions:</p>
     * <p>Log out from Backend.</p>
     */
    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Precondition fot test. Creating User with role Category/Manage Category.</p>
     *
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Catalog>Manage Category]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Expected results:</p>
     * <p> Admin user and role are successfully created.</p>
     * @test
     * @TestlinkId TL-MAGE-5955
     */
    public function roleResourceAccessManageCategory()
    {
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
            array('resource_1' => 'Catalog/Manage Categories'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        //return array $loginData to log in in the next test
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        return $loginData;
    }

    /**
     * <p>Deleting  Subcategory</p>
     * <p>Pre-Conditions:</p>
     * <p>1. Log in to admin with data of created admin user.</p>
     * <p>2. Subcategory created</p>
     * <p>3. Button "Add Root Category" isn't presented on page.
     * <p>Steps:</p>
     * <p>1. Log in to admin with data of created admin user.
     * <p>2. Select created Subcategory</p>
     * <p>3. Click "Delete" button</p>
     * <p>Expected result</p>
     * <p> Subcategory Deleted, Success message appears</p>
     *
     * @param array $loginData
     *
     * @depends roleResourceAccessManageCategory
     * @test
     * @TestlinkId TL-MAGE-3170
     */
    public function deleteSubCategory($loginData)
    {
        $this->admin('log_in_to_admin', false);
        $waitCondition = array($this->_getMessageXpath('general_error'), $this->_getMessageXpath('general_validation'),
                               $this->_getControlXpath('pageelement', 'admin_logo'));
        $this->fillFieldset($loginData, 'log_in');
        $this->clickButton('login', false);
        $this->waitForElement($waitCondition);
        $this->categoryHelper()->checkCategoriesPage();
        //Verifying that button "Add Root Category" doesn't present on page
        $xpath = $this->_getControlXpath('button', 'add_root_category');
        if ($this->isElementPresent($xpath)) {
            $this->fail("This user doesn't have permission to create root category. The button Create Root Category is present on page");
        }
        //Data
        $subCategoryData = $this->loadDataSet('Category', 'sub_category_required');
        //Steps
        $this->categoryHelper()->createCategory($subCategoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //Steps
        $this->categoryHelper()->selectCategory($subCategoryData['parent_category'] . '/' . $subCategoryData['name']);
        $this->categoryHelper()->deleteCategory('delete_category', 'confirm_delete');
        $this->assertMessagePresent('success', 'success_deleted_category');
    }
}
