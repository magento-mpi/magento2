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
    protected function assertPreConditions()
    {
        $this->markTestIncomplete('BUG: It is impossible to create any product');
        $this->admin('log_in_to_admin');
    }

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
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
            array('resource_acl' => 'products-inventory-catalog'));
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
        //Data
        $productData = $this->loadDataSet('Product', $type . '_product_required');
        $search = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $productData['general_sku']));
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->getParsedMessages());
        $this->productHelper()->createProduct($productData, $type);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->searchAndChoose($search, 'product_grid');

        $this->fillDropdown('mass_action_select_action', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
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
    public function roleResourceAccessWithoutProductPrice()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website', array(
            'resource_acl' => 'products-inventory-catalog',
            'resource_acl_skip' => 'products-inventory-catalog-read_product_price'
        ));
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
     * <p>Delete product without Price</p>
     *
     * @param string $type
     * @param array $loginData
     *
     * @test
     * @dataProvider deleteSingleProductWithoutPriceDataProvider
     * @depends roleResourceAccessWithoutProductPrice
     * @TestlinkId TL-MAGE-5966
     */
    public function deleteSingleProductWithoutPrice($type, $loginData)
    {
        //Data
        $productData = $this->loadDataSet('Product', $type . '_product_required',
            array('general_price' => '%noValue%'));
        $search = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $productData['general_sku']));
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->getParsedMessages());
        // Verifying visibility Price column
        $this->assertFalse($this->controlIsVisible('pageelement', 'product_grid_columns_price'),
            "This user doesn't have permission to watch Column Price");
        $this->productHelper()->createProduct($productData, $type);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->searchAndChoose($search, 'product_grid');

        $this->fillDropdown('mass_action_select_action', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
    }

    public function deleteSingleProductWithoutPriceDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable')
        );
    }
}

