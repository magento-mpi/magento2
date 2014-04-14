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
class Core_Mage_Acl_CmsStaticBlocksResourceOneRoleTest extends Mage_Selenium_TestCase
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
     * <p>Create Admin User with full CMS Static Block resources role</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTestCreateAdminUser()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'content-elements-blocks'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');

        return array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
    }

    /**
     * <p>Admin with Resource: CMS/Static Blocks has access to CMS/Static Blocks menu.</p>
     * <p>All necessary elements are presented</p>
     *
     * @param $loginData
     *
     * @depends preconditionsForTestCreateAdminUser
     * @test
     * @TestlinkId TL-MAGE-6138
     */
    public function verifyScopeCmsStaticBlockOneRoleResource($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_static_blocks'),
            $this->getParsedMessages());
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify  that necessary elements are present on page
        $elements = $this->loadDataSet('CmsStaticBlockPageElements', 'manage_cms_static_blocks_page_elements');
        $resultElementsArray = array();
        foreach ($elements as $key => $value) {
            $resultElementsArray = array_merge($resultElementsArray, (array_fill_keys(array_keys($value), $key)));
        }
        foreach ($resultElementsArray as $elementName => $elementType) {
            if (!$this->controlIsVisible($elementType, $elementName)) {
                $this->addVerificationMessage("Element type= '$elementType' name= '$elementName'"
                    . " is not present on the page");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Admin with Resource: CMS/Static Blocks can create new block with all fielded fields and conditions</p>
     *
     * @param $loginData
     *
     * @depends preconditionsForTestCreateAdminUser
     * @return array
     *
     * @test
     * @TestlinkId TL-MAGE-6140
     */
    public function createCmsStaticBlockOneRoleResource($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_cms_static_blocks'),
            $this->getParsedMessages());
        $setData = $this->loadDataSet('CmsStaticBlock', 'static_block_with_all_widgets',
            array('variables' => '%noValue%', 'widget_product_link' => '%noValue%'));
        $this->cmsStaticBlocksHelper()->createStaticBlock($setData);
        $this->assertMessagePresent('success', 'success_saved_block');
        return array(
            'filter_block_title' => $setData['block_title'],
            'filter_block_identifier' => $setData['block_identifier']
        );
    }

    /**
     * <p>Admin with Resource: CMS/Static Blocks can edit block and save using "Save And Continue Edit" button</p>
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
        $this->assertTrue($this->checkCurrentPage('manage_cms_static_blocks'),
            $this->getParsedMessages());
        $randomTitleAndId = array(
            'block_title' => $this->generate('string', 15),
            'block_identifier' => $this->generate('string', 15, ':lower:')
        );
        $this->cmsStaticBlocksHelper()->openStaticBlock($searchPageData);
        $this->fillFieldset($randomTitleAndId, 'general_information');
        $this->addParameter('elementTitle', $randomTitleAndId['block_title']);
        $this->saveAndContinueEdit('button', 'save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_block');
        $this->assertTrue($this->checkCurrentPage('edit_cms_static_block'),
            $this->getParsedMessages());
        $this->verifyForm($randomTitleAndId);
        $this->assertEmptyVerificationErrors();

        return array(
            'filter_block_title' => $randomTitleAndId['block_title'],
            'filter_block_identifier' => $randomTitleAndId['block_identifier']
        );
    }

    /**
     * <p>Admin with Resource: CMS/Static Blocks can delete static block</p>
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
        $this->assertTrue($this->checkCurrentPage('manage_cms_static_blocks'),
            $this->getParsedMessages());
        $this->cmsStaticBlocksHelper()->deleteStaticBlock($searchPageData);
        $this->assertMessagePresent('success', 'success_deleted_block');
    }
}
