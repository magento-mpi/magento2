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
class Enterprise_Mage_Acl_CmsPollTest extends Mage_Selenium_TestCase
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
     * <p>Preconditions</p>
     * <p>Create Admin User with full CMS pool resources and Main website scope role</p>
     *
     * @return array
     * @test
     */
    public function roleResourceAccessCmsPool()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
            array('resource_acl' => 'content-elements-polls'));
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
     * <p>Admin with Resource: CMS polls has access to CMS/pools menu. All necessary elements are presented</p>
     *
     * @param $loginData
     *
     * @depends roleResourceAccessCmsPool
     * @test
     * @TestlinkId TL-MAGE-6134
     */
    public function verifyScopeCmsPollOneRoleResource($loginData)
    {
        $elements = $this->loadDataSet('CmsPollElements', 'manage_cms_poll_elements');
        $resultElementsArray = array();
        // Verify that navigation menu has only 1 parent element
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->getParsedMessages());
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify  that necessary elements are present on page
        foreach ($elements as $key => $value) {
            $resultElementsArray = array_merge($resultElementsArray, (array_fill_keys(array_keys($value), $key)));
        }
        foreach ($resultElementsArray as $elementName => $elementType) {
            if (!$this->controlIsVisible($elementType, $elementName)) {
                $this->addVerificationMessage('Element type="' . $elementType . '" name="' . $elementName
                    . '" is not present on the page');
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Admin with Resource: CMS/Polls Scope: Main website can create new poll with all fielded fields</p>
     *
     * @param $loginData
     *
     * @depends roleResourceAccessCmsPool
     *
     * @return array
     *
     * @test
     * @TestlinkId TL-MAGE-6155
     */
    public function createNewPollForOneWebsite($loginData)
    {
        $pollData = $this->loadDataSet('CmsPoll', 'poll_open');
        $searchPollData = $this->loadDataSet('CmsPoll', 'search_poll',
            array('filter_question' => $pollData['poll_question']));
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->getParsedMessages());
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_poll');
        //Open Poll
        $this->cmsPollsHelper()->openPoll($searchPollData);
        //verify that buttons "Save poll" and "Delete" are not presented on page
        $this->assertFalse($this->controlIsVisible('button', 'save_poll'),
            "This user doesn't have permission to edit pull.");
        $this->assertFalse($this->controlIsVisible('button', 'delete_poll'),
            "This user doesn't have permission to delete pull.");
    }
}

