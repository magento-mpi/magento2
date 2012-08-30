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
class Enterprise2_Mage_ACL_CmsPollTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->clearInvalidedCache();
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore('StoreView/generic_store_view', 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
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
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
                                          array('resource_1' => 'CMS/Polls'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
                                array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        //return array $loginData to login in the next step
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        return $loginData;
    }

    /**
     * <p>Admin with Resource: CMS polls has access to CMS/pools menu. All necessary elements are presented</p>
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>Expected results:</p>
     * <p>1. Current page is Manage Polls </p>
     * <p>2. Navigation menu has only 1 parent element(CMS)</p>
     * <p>3. Navigation menu(CMS) has only 1 child element(Pages)</p>
     * <p>4. Manage Pools contains:</p>
     * <p>4.1 Buttons: "Add New Poll", "Reset Filter", "Search"</p>
     * <p>4.2 Fields: "filter_id", "filter_question", "filter_number_of_responses_from", "filter_umber_of_responses_to", "filter_date_posted_from", "filter_date_posted_to", "filter_date_closed_from","filter_date_closed_to"</p>
     * <p>4.3 Dropdown: "filter_status","filter_visible_in"</p>
     *
     * @param $loginData
     *
     * @depends roleResourceAccessCmsPool
     * @test
     * @TestlinkId TL-MAGE-6134
     */
    public function verifyScopeCmsPollOneRoleResource($loginData)
    {
        // Verify that navigation menu has only 1 parent element
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('poll_manager');
        $this->assertEquals('1', count($this->getElementsByXpath(
                $this->_getControlXpath('pageelement', 'navigation_menu_items'))),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 1 child elements
        $this->assertEquals('1', count($this->getElementsByXpath(
                $this->_getControlXpath('pageelement', 'navigation_children_menu_items'))),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify  that necessary elements are present on page
        $elements = $this->loadDataSet('CmsPollElements','manage_cms_poll_elements');
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
     * <p>Admin with Resource: CMS/Polls Scope: Main website can create new poll with all fielded fields</p>
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Click "Add New Poll" button</p>
     * <p>3. On "Poll Information" Tab fill all fields and select Sore View</p>
     * <p>4. On "Poll Answers" tab fill "Content Heading"   click "Add New Answer" button and add answer</p>
     * <p>5. Open newly created pull</p>
     * <p>Expected results: </p>
     * <p>1. CMS poll is created</p>
     * <p>2. Success Message is appeared "The poll has been saved."</p>
     * <p>3. The buttons 'Save pull' and 'Delete' aren't presented on edit pull page</p>
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
    public function  createNewPollForOneWebsite($loginData)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('poll_manager');
        $pollData = $this->loadDataSet('CmsPoll', 'poll_open');
        $name = $pollData['poll_question'];
        $searchPollData = $this->loadDataSet('CmsPoll', 'search_poll', array('filter_question' => $name));
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

