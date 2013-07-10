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

class Enterprise_Mage_Acl_CatalogManageCategoryTest extends Mage_Selenium_TestCase
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
     * <p>Precondition fot test. Creating User with role Category/Manage Category.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5955
     */
    public function roleResourceAccessManageCategory()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
            array('resource_acl' => 'products-inventory-categories'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        //Preconditions
        //create specific role with test roleResource
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');

        return array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
    }

    /**
     * <p>Deleting  Subcategory</p>
     *
     * @param array $loginData
     *
     * @depends roleResourceAccessManageCategory
     * @test
     * @TestlinkId TL-MAGE-3170
     */
    public function deleteSubCategory($loginData)
    {
        //Data
        $subCategoryData = $this->loadDataSet('Category', 'sub_category_required');
        //Steps
        $waitCondition = array(
            $this->_getMessageXpath('general_error'),
            $this->_getMessageXpath('general_validation'),
            $this->_getControlXpath('pageelement', 'admin_logo')
        );
        $this->fillFieldset($loginData, 'log_in');
        $this->clickButton('login', false);
        $this->waitForElement($waitCondition);
        $this->pleaseWait();
        $this->categoryHelper()->checkCategoriesPage();
        //Verifying that button "Add Root Category" doesn't present on page
        $this->assertFalse($this->buttonIsPresent('add_root_category'),
            'This user have permission to create root category. The button Create Root Category is present on page');
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
