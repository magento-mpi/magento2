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
class Enterprise_Mage_Tags_AdminGwsTest extends Mage_Selenium_TestCase
{
    protected function tearDownAfterTestClass()
    {
        $this->logoutAdminUser();
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
    public function mainPreconditions()
    {
        $this->loginAdminUser();

        // Create root category
        $categoryData = $this->loadDataSet('Category', 'root_category_required');
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();

        // Create sub category
        $subCategoryData = $this->loadDataSet('Category', 'sub_category_required');
        $subCategoryData['parent_category'] = $categoryData['name'];
        $this->categoryHelper()->createCategory($subCategoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();

        // Create website, store, store view
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

        // Create new role
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role',
            array(
                'role_resources_tab' => array(
                    'role_scopes' => array(
                        'scopes' => 'Custom',
                        'website_1' => $websiteData['website_name']
                    ),
                    'role_resources' => array(
                        'resource_access' => 'All'
                    )
                )
            )
        );
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');

        // Create new backend user
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array(
                'role_name' => $roleSource['role_info_tab']['role_name'],
            )
        );
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');

        // Create new product
        $simple = $this->loadDataSet('Product', 'simple_product_visible', array(
            'websites'   => 'Main Website,' . $websiteData['website_name'],
            'general_categories' => 'Default Category,' . $categoryData['name']
        ));
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');

        // Create new customer
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array(
            'customer'   => $userData,
            'product'    => $simple['general_name'],
            'admin_user' => array(
                'user_name' => $testAdminUser['user_name'],
                'password'  => $testAdminUser['password']
            )
        );
    }

    /**
     * @test
     * @depends mainPreconditions
     *
     * @param array $testData
     * @return array
     */
    public function preconditionCreateTags(array $testData)
    {
        $this->markTestIncomplete('BUG: There is no tag_accepted_success message after add tag');
        // Go to frontend and post new tag
        $data = array(
            'email'    => $testData['customer']['email'],
            'password' => $testData['customer']['password']
        );
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($data);

        // Post new tag for created product
        $tags = array();
        $testTag = '';
        for ($i = 0; $i < 2; $i++) {
            $testTag = $this->generate('string', 7, ':alpha:');
            $this->productHelper()->frontOpenProduct($testData['product']);
            $this->tagsHelper()->frontendAddTag($testTag);
            $this->assertMessagePresent('success', 'tag_accepted_success');
            $tags[$testTag] = 'Pending';
        }
        $this->logoutCustomer();

        // Go to backend and set status "Approved" for created tag
        $this->loginAdminUser();
        $this->navigate('all_tags');
        $this->tagsHelper()->changeTagsStatus(
            array(
                array('tag_name' => $testTag)
            ),
            'Approved'
        );
        $tags[$testTag] = 'Approved';
        $testData['tags'] = $tags;

        return $testData;
    }

    /**
     * Check restrictions to Products Tag Report for admin with access to specific website.
     *
     * @test
     * @param array $testData
     * @depends preconditionCreateTags
     * @TestlinkId TL-MAGE-6107, TL-MAGE-6114
     */
    public function productsTagReportAccess(array $testData)
    {
        $this->loginAdminUser();
        // Check that product existed in products tag report
        $this->navigate('report_tag_product');
        $this->assertNotNull($this->reportsHelper()->searchDataInReport(array(
            'product_name' => $testData['product'],
            'unique_tags_number' => 1,
            'total_tags_number' => 1,
        )), 'Product with submitted tag is not shown in report');

        $this->logoutAdminUser();

        // Login into backend as user which created before and check that product not existed in product tag report
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
     * @test
     * @param array $testData
     * @depends preconditionCreateTags
     * @TestlinkId TL-MAGE-6114
     */
    public function customersTagReportAccess(array $testData)
    {
        $this->loginAdminUser();

        // Check that customer existed in customers tag report
        $this->navigate('report_tag_customer');
        $this->assertNotNull($this->reportsHelper()->searchDataInReport(array(
            'first_name' => $testData['customer']['first_name'],
            'last_name' => $testData['customer']['last_name'],
            'total_tags' => 1,
        )), 'Customer who submitted tag is not shown in report');

        $this->logoutAdminUser();

        // Login into backend as user which created before and check that product not existed in product tag report
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

    /**
     * Check restrictions to Customers Tag Report for admin with access to specific website.
     *
     * @test
     * @param array $testData
     * @depends preconditionCreateTags
     * @TestlinkId TL-MAGE-6118
     */
    public function editTagAccess(array $testData)
    {
        $this->logoutAdminUser();
        // Login as user with access only to specific website
        $loginData = array(
            'user_name' => $testData['admin_user']['user_name'],
            'password'  => $testData['admin_user']['password']
        );
        $this->adminUserHelper()->loginAdmin($loginData);

        // Checking created tags and statuses of this tags
        foreach ($testData['tags'] as $tag => $tagStatus) {
            $this->navigate('all_tags');
            $this->tagsHelper()->verifyTag(array('tag_name' => $tag, 'status' => $tagStatus));
        }

        // Checking that user has no possibility to delete tags via mass action
        $this->navigate('all_tags');
        $xpath = $this->_getControlXpath('dropdown', 'tags_massaction');
        $this->assertFalse($this->elementIsPresent($xpath . "//option[text()='Delete']"),
            'Action "Delete" must be absent'
        );

        // Trying to delete pending tag and verify error message appearing
        $pendingTag = '';
        foreach ($testData['tags'] as $tag => $status) {
            if ($status == 'Pending') {
                $pendingTag = $tag;
            }
        }
        $this->navigate('all_tags');
        $this->searchAndChoose(array('tag_name' => $pendingTag), 'tags_grid');
        $this->fillDropdown('tags_massaction', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete');
        $this->assertMessagePresent('error', 'error_deleted_tag_has_no_permissions');

        // Checking that action buttons "Delete Tag", "Save Tag", "Save and continue edit" are absent on tag edit page.
        $this->tagsHelper()->openTag(array('tag_name' => $pendingTag));
        $buttonListForCheck = array('delete_tag', 'save_tag', 'save_and_continue_edit');
        foreach ($buttonListForCheck as $buttonName) {
            $xpath = $this->_getControlXpath('button', $buttonName);
            $this->assertFalse($this->elementIsPresent($xpath));
        }

        // Checking that edit tag fields are disabled
        $fieldListForCheck = array(
            'tag_name'        => 'field',
            'tag_status'      => 'dropdown',
            'base_popularity' => 'field'
        );
        foreach ($fieldListForCheck as $fieldName => $fieldType) {
            $this->assertFalse($this->controlIsEditable($fieldType, $fieldName));
        }

        $this->logoutAdminUser();
    }
}
