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
class Core_Mage_Acl_CmsPollTest extends Mage_Selenium_TestCase
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
     * <p>Create Admin User with full CMS pool resources role</p>
     *
     * @return array
     * @test
     */
    public function roleResourceAccessCmsPool()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'content-elements-polls'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
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
     * <p>Admin with Resource: CMS/Polls can create new poll with all fielded fields</p>
     *
     * @param $loginData
     *
     * @depends roleResourceAccessCmsPool
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6135
     */
    public function createNewPoll($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->getParsedMessages());
        $this->cmsPollsHelper()->closeAllPolls();
        $pollData = $this->loadDataSet('CmsPoll', 'poll_open');
        $searchPollData = $this->loadDataSet('CmsPoll', 'search_poll',
            array('filter_question' => $pollData['poll_question']));
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_poll');
        $this->cmsPollsHelper()->openPoll($searchPollData);
        $this->cmsPollsHelper()->checkPollData($pollData);
        return $pollData;
    }

    /**
     * <p>Admin with Resource: CMS/Polls can edit cms poll and save using "Save Poll" button</p>
     *
     * @param $loginData
     * @param $pollData
     *
     * @depends roleResourceAccessCmsPool
     * @depends createNewPoll
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6136
     */
    public function editPoll($loginData, $pollData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->getParsedMessages());
        $this->cmsPollsHelper()->closeAllPolls();
        $searchPollData = $this->loadDataSet('CmsPoll', 'search_poll',
            array('filter_question' => $pollData['poll_question']));
        //Steps
        $this->cmsPollsHelper()->setPollState($searchPollData, 'Open');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_poll');
        return $searchPollData;
    }

    /**
     * <p>Admin with Resource: CMS/Polls can delete cms poll</p>
     *
     * @param $loginData
     * @param $searchPollData
     *
     * @depends roleResourceAccessCmsPool
     * @depends editPoll
     * @depends createNewPoll
     *
     * @test
     * @TestlinkId TL-MAGE-6137
     *
     */
    public function deleteNewPoll($loginData, $searchPollData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('poll_manager'), $this->getParsedMessages());
        //Steps
        $this->cmsPollsHelper()->deletePoll($searchPollData);
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_poll');
    }
}
