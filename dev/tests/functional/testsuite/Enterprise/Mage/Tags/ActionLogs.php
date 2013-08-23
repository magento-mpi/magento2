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
 * Reward Points for Tags
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Tags_ActionLogsTest extends Mage_Selenium_TestCase
{
    protected function tearDownAfterTestClass()
    {
        $this->logoutAdminUser();
    }

    /**
     * Precondition:
     * 1. Create admin user
     *
     * @return array
     * @test
     */
    public function preconditionsTest()
    {
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => 'Administrators'));
        $loginData = array('user_name' => $userData['user_name'], 'password' => $userData['password']);
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        //generate events
        //save
        $tagData = $this->loadDataSet('Tag', 'backend_new_tag');
        $this->navigate('all_tags');
        $this->tagsHelper()->addTag($tagData);
        //edit
        $this->tagsHelper()->openTag($tagData);
        //massState
        $this->navigate('all_tags');
        $this->tagsHelper()->changeTagsStatus(array($tagData), 'Pending');
        //massDelete
        $tagData['tag_status'] = 'Pending';
        $this->searchAndChoose($tagData, 'tags_grid');
        $this->fillDropdown('tags_massaction', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete');
        $this->addParameter('qtyDeletedTags', '1');
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
        //Delete
        $tagData = $this->loadDataSet('Tag', 'backend_new_tag');
        $this->navigate('all_tags');
        $this->tagsHelper()->addTag($tagData);
        $this->tagsHelper()->openTag($tagData);
        $this->clickButtonAndConfirm('delete_tag', 'confirmation_for_delete');
        $this->assertMessagePresent('success', 'success_deleted_tag');
        return $userData;
    }

    /**
     * Action Logs for Tags Management
     *
     * @param array $userData
     * @param string $action
     * @param string $actionFullName
     *
     * @test
     * @depends preconditionsTest
     * @dataProvider tagActionsDataProvider
     * @TestlinkId TL-MAGE-6108
     */
    public function actionLogs($action, $actionFullName, $userData)
    {
        $this->loginAdminUser();
        $this->navigate('admin_action_log_report');
        $this->assertNotNull($this->search(array(
                    'action_group' => 'Catalog Tags',
                    'action_username' => $userData['user_name'],
                    'action' => $action,
                    'action_result' => 'Success',
                    'action_full_name' => $actionFullName), 'action_logs_grid'
            ),
            "Admin Action Logs does not contain {$action}"
        );
    }

    public function tagActionsDataProvider()
    {
        return array(
            array('Save', 'adminhtml_tag_save'),
            array('View', 'adminhtml_tag_edit'),
            array('Delete', 'adminhtml_tag_delete'),
            array('Mass Delete', 'adminhtml_tag_massDelete'),
            array('Mass Update', 'adminhtml_tag_massStatus')
        );
    }
}