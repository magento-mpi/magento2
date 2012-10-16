<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Acl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Community2_Mage_ACL_CmsStaticBlocksResourceOneRoleTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->admin('log_in_to_admin', false);
    }

    protected function tearDownAfterTest()
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
    }

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore('StoreView/generic_store_view', 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
    }

    /**
     * <p>Create Admin User with full CMS Static Block resources role</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTestCreateAdminUser()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
                                       array('resource_1' => 'CMS/Static Blocks'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');

        return  array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
    }

    /**
     * <p>Admin with Resource: CMS/Static Blocks has access to CMS/Static Blocks menu. All necessary elements are presented</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>Expected results:</p>
     * <p>1. Current page is Manage Pages </p>
     * <p>2. Navigation menu has only 1 parent element(CMS)</p>
     * <p>3. Navigation menu(CMS) has only 1 child element(Static Blocks)</p>
     * <p>4. Manage Pages contains:</p>
     * <p>4.1 Buttons: "Add New Block", "Reset Filter", "Search"</p>
     * <p>4.2 Fields: "filter_block_title", "filter_block_identifier", "filter_block_created_from", "filter_block_created_to", "filter_block_modified_from", "filter_block_modified_to"</p>
     * <p>4.3 Dropdowns: "filter_store_view", "filter_block_status"</p>
     *
     * @param $loginData
     * @depends preconditionsForTestCreateAdminUser
     *
     * @test
     * @TestlinkId TL-MAGE-6138
     */
    public function verifyScopeCmsStaticBlockOneRoleResource($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_cms_static_blocks');
        $this->assertEquals('1', count($this->getElementsByXpath(
                $this->_getControlXpath('pageelement', 'navigation_menu_items'))),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals('1', count($this->getElementsByXpath(
                $this->_getControlXpath('pageelement', 'navigation_children_menu_items'))),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify  that necessary elements are present on page
        $elements= $this->loadDataSet('CmsStaticBlockPageElements','manage_cms_static_blocks_page_elements');
        $resultElementsArray = array();
        foreach ($elements as $key => $value) {
            $resultElementsArray = array_merge($resultElementsArray, (array_fill_keys(array_keys($value), $key)));
        }
        foreach ($resultElementsArray as $elementName => $elementType) {
            if (!$this->controlIsVisible($elementType, $elementName)) {
                $this->addVerificationMessage("Element type= '$elementType'
                                                       name= '$elementName' is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Admin with Resource: CMS/Static Blocks can create new block with all fielded fields and conditions</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Click "Add New Block" button</p>
     * <p>3. Fill all fields with valid data</p>
     * <p>4. Select Store View  and click "Show / Hide Editor" button</p>
     * <p>4.1 Click "Insert Widgets..." button and add one of each type of widgets</p>
     * <p>Expected results: </p>
     * <p>1. Static Block is created</p>
     * <p>2. Success Message is appeared "The block has been saved."</p>
     *
     * @param $loginData
     * @depends preconditionsForTestCreateAdminUser
     * @return array
     *
     * @test
     * @TestlinkId TL-MAGE-6140
     */
    public function createCmsStaticBlockOneRoleResource($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_cms_static_blocks');
        $setData = $this->loadDataSet('CmsStaticBlock', 'static_block_with_all_widgets');
        unset($setData['content']['variables']);
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        $this->assertMessagePresent('success', 'success_saved_block');
        return array('filter_block_title'      => $setData['block_title'] ,
                     'filter_block_identifier' => $setData['block_identifier']);
    }

    /**
     * <p>Admin with Resource: CMS/Static Blocks can edit block and save using "Save And Continue Edit" button</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Find test static block in grid and click</p>
     * <p>3. Fill "Block Title" and "Identifier" fields with any new value</p>
     * <p>4. Click "Save And Continue Edit" button</p>
     * <p>Expected results:</p>
     * <p>1. Block is saved</p>
     * <p>2. Current page is "Edit Block '%Block Title%'"</p>
     * <p>3. Success Message is appeared "The block has been saved."</p>
     *
     * @param $loginData
     * @param $searchPageData
     *
     * @depends preconditionsForTestCreateAdminUser
     * @depends createCmsStaticBlockOneRoleResource
     * @return array
     *
     * @test
     * @TestlinkId TL-MAGE-6143
     */
    public function editCmsStaticBlockOneRoleResource($loginData, $searchPageData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_cms_static_blocks');
        $randomTitleAndIdentifier = array('block_title'      => $this->generate('string', 15),
                                          'block_identifier' => $this->generate('string', 15));
        $this->cmsStaticBlocksHelper()->openStaticBlock($searchPageData);
        $this->fillFieldset($randomTitleAndIdentifier, 'general_information');
        $this->clickControlAndWaitMessage('button', 'save_and_continue_edit', false);
        $this->addParameter('blockName', $randomTitleAndIdentifier['block_title']);
        $this->validatePage('edit_cms_static_block');
        $this->assertMessagePresent('success', 'success_saved_block');

        return array('filter_block_title'      => $randomTitleAndIdentifier['block_title'] ,
                     'filter_block_identifier' => $randomTitleAndIdentifier['block_identifier']);
    }

    /**
     * <p>Admin with Resource: CMS/Static Blocks can delete static block</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Find test static block in grid and click</p>
     * <p>3. Click "Delete Page" button</p>
     * <p>4. Click "OK" button for confirm action</p>
     * <p>Expected results:</p>
     * <p>1. Block is deleted</p>
     * <p>2. Success Message is appeared "The block has been deleted."</p>
     *
     * @param $loginData
     * @param $searchPageData
     *
     * @depends  preconditionsForTestCreateAdminUser
     * @depends editCmsStaticBlockOneRoleResource
     *
     * @test
     * @TestlinkId TL-MAGE-6145
     */
    public function deleteCmsStaticBlockOneRoleResource($loginData, $searchPageData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_cms_static_blocks');
        $this->cmsStaticBlocksHelper()->deleteStaticBlock($searchPageData);
        $this->assertMessagePresent('success', 'success_deleted_block');
    }
}