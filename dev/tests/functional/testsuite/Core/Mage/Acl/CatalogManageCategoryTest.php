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
class Core_Mage_Acl_CatalogManageCategoryTest extends Mage_Selenium_TestCase
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
     * <p>Precondition fot test. Creating User with role Catalog/Manage Category</p>
     *
     * @return array $loginData
     *
     * @test
     * @TestlinkId TL-MAGE-5955
     */
    public function roleResourceAccessManageCategory()
    {
        //Preconditions
        //create specific role with test roleResource
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'products-inventory-categories'));
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
        //return array $loginData to login in the next step
        return array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
    }

    /**
     * <p>Deleting Root Category</p>
     *
     * @param array $loginData
     *
     * @depends roleResourceAccessManageCategory
     * @test
     * @TestlinkId TL-MAGE-3167
     */
    public function rootCategoryWithRequiredFieldsOnly($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->categoryHelper()->checkCategoriesPage();
        //Data
        $rootCategoryData = $this->loadDataSet('Category', 'root_category_required');
        //Steps
        $this->categoryHelper()->createCategory($rootCategoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //Steps
        $this->categoryHelper()->selectCategory($rootCategoryData['name']);
        $this->categoryHelper()->deleteCategory('delete_category', 'confirm_delete');
        $this->assertMessagePresent('success', 'success_deleted_category');
    }

    /**
     * <p> Deleting  Subcategory</p>
     *
     * @param array $loginData
     *
     * @depends roleResourceAccessManageCategory
     * @test
     * @TestlinkId TL-MAGE-3170
     */
    public function deleteSubCategory($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->categoryHelper()->checkCategoriesPage();
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
