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
class Core_Mage_Acl_CmsPageResourceOneRoleTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore('StoreView/generic_store_view', 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        $this->logoutAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->admin('log_in_to_admin');
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category to use during tests</p>
     *
     * @return array
     *
     * @test
     */
    public function preconditionsForTestsCreateCategory()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $product = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $category['parent_category'] . '/' . $category['name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('category_path' => $product['general_categories'], 'filter_sku' => $product['general_sku']);
    }

    /**
     * <p>Create Admin User with full CMS pages resources role</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTestCreateAdminUser()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'content-elements-pages'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);

        return $loginData;
    }

    /**
     * <p>Admin with Resource: CMS pages has access to CMS/page menu. All necessary elements are presented</p>
     *
     * @param $loginData
     *
     * @test
     * @depends preconditionsForTestCreateAdminUser
     * @TestlinkId TL-MAGE-6129
     */
    public function verifyScopeCmsPageOneRoleResource($loginData)
    {
        // Verify that navigation menu has only 1 parent element
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_pages'), $this->getParsedMessages());
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Children elements not equal 1 should be equal');
        // Verify  that necessary elements are present on page
        $elements = $this->loadDataSet('CmsPageElements', 'manage_cms_pages_elements');
        $resultElementsArray = array();
        foreach ($elements as $key => $value) {
            $resultElementsArray = array_merge($resultElementsArray, (array_fill_keys(array_keys($value), $key)));
        }
        foreach ($resultElementsArray as $elementName => $elementType) {
            if (!$this->controlIsVisible($elementType, $elementName)) {
                $this->addVerificationMessage("Element type= '$elementType' name= '$elementName' "
                    . "is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Admin with Resource: CMS/Pages can create new page with all fielded fields</p>
     *
     * @param $data
     * @param $loginData
     *
     * @depends preconditionsForTestsCreateCategory
     * @depends preconditionsForTestCreateAdminUser
     * @depends verifyScopeCmsPageOneRoleResource
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6131
     */
    public function createCmsPageOneRoleResource($data, $loginData)
    {
        $this->markTestIncomplete('MAGETWO-8415');
        $this->adminUserHelper()->loginAdmin($loginData);
        $pageData = $this->loadDataSet('CmsPage', 'new_page_all_fields', $data);
        unset($pageData['content']['variable_data']);
        //Steps
        $this->navigate('manage_cms_pages');
        $this->cmsPagesHelper()->createCmsPage($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_page');
        $this->cmsPagesHelper()->frontValidatePage($pageData);

        return array(
            'filter_title' => $pageData['page_information']['page_title'],
            'filter_url_key' => $pageData['page_information']['url_key']
        );
    }

    /**
     * <p>Admin with Resource: CMS/Pages can edit cms page and save using "Save And Continue Edit" button</p>
     *
     * @param $loginData
     * @param $searchPageData
     * @return array
     *
     * @depends preconditionsForTestCreateAdminUser
     * @depends createCmsPageOneRoleResource
     * @depends verifyScopeCmsPageOneRoleResource
     * @test
     * @TestlinkId TL-MAGE-6132
     */
    public function editCmsPageOneRoleResource($loginData, $searchPageData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_pages'), $this->getParsedMessages());

        $this->cmsPagesHelper()->openCmsPage($searchPageData);
        $randomName = array('page_title' => $this->generate('string', 15));
        $this->fillFieldset($randomName, 'page_information_fieldset');
        $this->addParameter('elementTitle', $randomName['page_title']);
        $this->saveAndContinueEdit('button', 'save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_cms_page');
        $this->assertTrue($this->checkCurrentPage('save_and_continue_edit_cms_page'),
            $this->getParsedMessages());

        return array(
            'filter_title' => $randomName['page_title'],
            'filter_url_key' => $searchPageData['filter_url_key']
        );
    }

    /**
     * <p>Admin with Resource: CMS/Pages can delete cms page</p>
     *
     * @param $loginData
     * @param $searchPageData
     *
     * @depends preconditionsForTestCreateAdminUser
     * @depends editCmsPageOneRoleResource
     * @depends verifyScopeCmsPageOneRoleResource
     *
     * @test
     * @TestlinkId TL-MAGE-6133
     */
    public function deleteCmsPageOneRoleResource($loginData, $searchPageData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_pages'), $this->getParsedMessages());
        //Steps
        $this->cmsPagesHelper()->deleteCmsPage($searchPageData);
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_cms_page');
    }
}
