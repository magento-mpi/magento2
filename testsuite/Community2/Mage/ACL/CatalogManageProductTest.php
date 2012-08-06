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

class Community2_Mage_ACL_CatalogManageProductTest extends Mage_Selenium_TestCase
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
     * <p>Precondition fot test. Creating User with role Catalog/Manage Products</p>
     *
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Catalog>Manage Products]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Expected results:</p>
     * <p>1. Role and user are successfully created. </p>
     * @test
     * @TestlinkId TL-MAGE-5956
     */
    public function roleResourceAccessManageProduct()
    {
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Catalog/Manage Products'));
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
        //return array $loginData
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        return $loginData;
    }

    /**
     *
     * <p>Delete product.</p>
     * <p>Preconditions</p>
     * <p>1. Log in admin with created user. </p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     * <p>6. Open product;</p>
     * <p>7. Click "Delete" button;</p>
     * <p>Expected result:</p>
     * <p>Product is deleted, confirmation message appears;</p>
     *
     * @param string $type
     * @param array $loginData
     *
     * @depends roleResourceAccessManageProduct
     * @test
     * @dataProvider deleteSingleProductDataProvider
     * @TestlinkId TL-MAGE-3425
     */
    public function deleteSingleProduct($type, $loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_products');
        //Data
        $productData = $this->loadDataSet('Product', $type . '_product_required');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, $type);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_product');
    }

    public function deleteSingleProductDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable'),
            array('grouped'),
            array('bundle')
        );
    }
}

