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

class Enterprise_Mage_Acl_CatalogManageProductTest extends Mage_Selenium_TestCase
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
     * <p> Precondition fot test. Creating User with role Catalog/Manage Products</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5956
     */
    public function roleResourceAccessManageProduct()
    {
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
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
        //return array $loginData to login in next test
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        return $loginData;
    }

    /**
     *
     * <p>Delete product.</p>
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
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_products');
        //Data
        $productData = $this->loadDataSet('Product', $type . '_product_website');
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

    /**
     * <p>Precondition fot test. Creating User with role Catalog/Manage Products</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5965
     */
    public function roleResourceAccessEditProductStatus()
    {
        $this->admin('log_in_to_admin', false);
        $this->loginAdminUser();
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
            array('resource_1' => 'Catalog/Manage Products/Edit Product Status'));
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
        //return array $loginData to log in in next test
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        return $loginData;
    }

    /**
     *
     * <p>Delete product;</p>
     *
     * @param string $type
     * @param array $loginData
     *
     * @depends roleResourceAccessEditProductStatus
     * @test
     * @dataProvider deleteSingleProductDataProvider1
     * @TestlinkId TL-MAGE-5966
     */
    public function deleteSingleProduct_without_Price($type, $loginData)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_products');
        // Verifying visibility Price column
        if ($this->controlIsPresent('pageelement', 'product_grid_columns_price')) {
            $this->fail("This user doesn't have permission to watch Column Price");
        }
        //Data
        $productData = $this->loadDataSet('Product', $type . '_product_price');
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

    public function deleteSingleProductDataProvider1()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable'),
        );
    }
}

