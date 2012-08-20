<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tags
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Check restrictions to Tag Information for admin with access to specific website
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Tags_AdminGwsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->deleteAllTags();
        $this->logoutAdminUser();
    }

    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForReportTest()
    {
        /**
         * Create root category
         */
        $categoryData = $this->loadDataSet('Category', 'root_category_required');
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        /**
         * Create sub category
         */
        $subCategoryData = $this->loadDataSet('Category', 'sub_category_required');
        $subCategoryData['parent_category'] = $categoryData['name'];
        $this->categoryHelper()->createCategory($subCategoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        /**
         * Create website, store, store view
         */
        $this->navigate('manage_stores');
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        $this->storeHelper()->createStore($websiteData, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('website' => $websiteData['website_name']));
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $storeViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        /**
         * Create new role
         */
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role',
            array(
                'role_resources_tab' => array(
                    'role_scopes' => array(
                        'scopes' => 'Custom',
                        'website_1' => $websiteData['website_name']),
                    'role_resources' => array('resource_access' => 'All'))));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        /**
         * Create new backend user
         */
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array(
                'role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        /**
         * Create new product
         */
        $simple = $this->loadDataSet('Product', 'simple_product_visible', array(
            'websites'   => 'Main Website,' . $websiteData['website_name'],
            'categories' => 'Default Category,' . $categoryData['name']));
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        /**
         * Create new customer
         */
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        /**
         * Go to frontend and create new customer
         */
        $data = array('email' => $userData['email'],'password' => $userData['password']);
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($data);
        /**
         * Post new tag for created product
         */
        $testTag = $this->generate('string', 7, ':alpha:');
        $this->productHelper()->frontOpenProduct($simple['general_name']);
        $this->tagsHelper()->frontendAddTag($testTag);
        $this->assertMessagePresent('success', 'tag_accepted_success');
        $this->logoutCustomer();
        /**
         * Go to backend and set status "Approved" for created tag
         */
        $this->loginAdminUser();
        $this->navigate('pending_tags');
        $this->tagsHelper()->changeTagsStatus(array(
                array('tag_name' => $testTag)), 'Approved');
        return array(
            'customer'   => $userData,
            'product'    => $simple['general_name'],
            'admin_user' => array(
                'user_name' => $testAdminUser['user_name'],
                'password'  => $testAdminUser['password']));
    }
    /**
     * Check restrictions to Products Tag Report for admin with access to specific website.
     *
     * 1. Login to backendCreate new root category and test sub category.
     * 2. Create new website, store and store view: System -> Manage Stores
     * 3. Create new role with scope access to website which created on step 1 and resource access "All"
     * 4. Create new backend user with role which created on step 3.
     * 5. Create new product available for both websites.
     * 6. Go to frontend. Open default website and find product which created on step 5.
     *    Post at least one new tag for this product.
     * 7. Go to backend. Open created tags from backend and set them status "Approved".
     * 8. Go to Reports -> Tags -> Products and find tagged product in report grid.
     * 9. Login into backend as user created on step 4 and repeat step 8.
     *    Expected:   Tagged products not existed in report grid.
     *
     * @param array $testData
     * @depends preconditionsForReportTest
     * @TestlinkId TL-MAGE-6107, TL-MAGE-6114
     */
    public function testProductsTagReportAccess(array $testData)
    {
        /**
         * Check that product existed in products tag report
         */
        $this->navigate('report_tag_product');
        $this->assertNotNull($this->reportsHelper()->searchDataInReport(array(
            'product_name' => $testData['product'],
            'unique_tags_number' => 1,
            'total_tags_number' => 1,
        )), 'Product with submitted tag is not shown in report');

        $this->logoutAdminUser();

        /**
         * Login into backend as user which created before and check that product not existed in product tag report
         */
        $loginData = array(
            'user_name' => $testData['admin_user']['user_name'],
            'password'  => $testData['admin_user']['password']
        );
        $this->adminUserHelper()->loginAdmin($loginData);

        $this->navigate('report_tag_product');
        $this->assertNull($this->reportsHelper()->searchDataInReport(array(
            'product_name' => $testData['product'],
            'unique_tags_number' => 1,
            'total_tags_number' => 1,
        )), 'Product with submitted tag must not shown in report');

        $this->logoutAdminUser();
    }

    /**
     * Check restrictions to Customers Tag Report for admin with access to specific website.
     *
     * 1. Login to backendCreate new root category and test sub category.
     * 2. Create new website, store and store view: System -> Manage Stores
     * 3. Create new role with scope access to website which created on step 1 and resource access "All"
     * 4. Create new backend user with role which created on step 3.
     * 5. Create new product available for both websites.
     * 6. Go to frontend. Open default website and find product which created on step 5.
     *    Post at least one new tag for this product.
     * 7. Go to backend. Open created tags from backend and set them status "Approved".
     * 8. Go to Reports -> Tags -> Customers and find customer who tagged product in report grid.
     * 9. Login into backend as user created on step 4 and repeat step 8.
     *    Expected:   Customer who tagged product must not existed in report grid.
     *
     * @param array $testData
     * @depends preconditionsForReportTest
     * @TestlinkId TL-MAGE-6114
     */
    public function testCustomersTagReportAccess(array $testData)
    {
        /**
         * Check that customer existed in customers tag report
         */
        $this->navigate('report_tag_customer');
        $this->assertNotNull($this->reportsHelper()->searchDataInReport(array(
            'first_name' => $testData['customer']['first_name'],
            'last_name' => $testData['customer']['last_name'],
            'total_tags' => 1,
        )), 'Customer who submitted tag is not shown in report');

        $this->logoutAdminUser();

        /**
         * Login into backend as user which created before and check that product not existed in product tag report
         */
        $loginData = array(
            'user_name' => $testData['admin_user']['user_name'],
            'password'  => $testData['admin_user']['password']
        );
        $this->adminUserHelper()->loginAdmin($loginData);

        $this->navigate('report_tag_customer');
        $this->assertNull($this->reportsHelper()->searchDataInReport(array(
            'first_name' => $testData['customer']['first_name'],
            'last_name' => $testData['customer']['last_name'],
            'total_tags' => 1,
        )), 'Customer who submitted tag must not shown in report');

        $this->logoutAdminUser();
    }
}
