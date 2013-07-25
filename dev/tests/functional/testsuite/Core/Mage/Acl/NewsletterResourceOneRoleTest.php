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
class Core_Mage_Acl_NewsletterResourceOneRoleTest extends Mage_Selenium_TestCase
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
     * Create Admin User with full Newsletter resources role
     *
     * @return array $loginData
     *
     * @test
     */
    public function createAdminUser()
    {
        //Data
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-communications'));
        //Create role
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //Create user
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');

        return array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
    }

    /**
     * Admin with Resource: Newsletter has access to Newsletter menu. All necessary buttons are presented
     *
     * @param array $loginData
     *
     * @return array
     *
     * @test
     * @depends createAdminUser
     * @TestlinkId TL-MAGE-6034
     */
    public function verifyScopeNewsletterResource($loginData)
    {
        $this->markTestIncomplete('MAGETWO-11337');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        // Verify that navigation menu has only 1 parent element
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        $this->assertTrue($this->buttonIsPresent('add_new_template'),
            'There is no "Add New Template" button on the page');
        $this->clickButton('add_new_template');
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_template'), 'There is no "Save Template" button on the page');
        $this->assertTrue($this->buttonIsPresent('preview_template'), 'There is no "Preview" button on the page');
    }

    /**
     * Admin with Resource: Newsletter can create new newsletter template
     *
     * @param $loginData
     *
     * @return array $newsData
     *
     * @test
     * @depends createAdminUser
     * @TestlinkId TL-MAGE-6035
     */
    public function createNewsletterResourceOneRole($loginData)
    {
        //Data
        $newsData = $this->loadDataSet('Newsletter', 'generic_newsletter_data');
        $searchData = $this->newsletterHelper()->convertToFilter($newsData);
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('newsletter_templates');
        $this->newsletterHelper()->createNewsletterTemplate($newsData);
        //Verifying
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_saved_newsletter');
        $this->assertNotNull($this->search($searchData, 'newsletter_templates_grid'),
            'Template( Name: ' . $newsData['newsletter_template_name'] . ' ) is not presented in grid');

        return $newsData;
    }

    /**
     * Admin with Resource: Newsletter can edit and save exists newsletter template
     *
     * @param array $loginData
     * @param array $newsData
     *
     * @return array $newNewsletterData
     *
     * @test
     * @depends createAdminUser
     * @depends createNewsletterResourceOneRole
     * @TestlinkId TL-MAGE-6036
     */
    public function editNewsletterResourceOneRole($loginData, $newsData)
    {
        //Data
        $newNewsletterData = $this->loadDataSet('Newsletter', 'edit_newsletter');
        $searchData = $this->newsletterHelper()->convertToFilter($newNewsletterData);
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('newsletter_templates');
        $this->newsletterHelper()->editNewsletter($newsData, $newNewsletterData);
        //Verifying
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_saved_newsletter');
        $this->assertNotNull($this->search($searchData, 'newsletter_templates_grid'),
            'Template (Name: ' . $newNewsletterData['newsletter_template_name'] . ') is not presented in grid');

        return $newNewsletterData;
    }

    /**
     * Admin with Resource: Newsletter can put newsletter template to queue
     *
     * @param array $loginData
     * @param $newNewsletterData
     *
     * @depends createAdminUser
     * @depends editNewsletterResourceOneRole
     *
     * @test
     * @TestlinkId TL-MAGE-6037
     */
    public function putNewsToQueueOneRole($loginData, $newNewsletterData)
    {
        //Data
        $newData = $this->loadDataSet('Newsletter', 'edit_newsletter_before_queue',
            array('newsletter_queue_data' => '12.12.12'));
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('newsletter_templates');
        $this->newsletterHelper()->putNewsToQueue($newNewsletterData, $newData);
        //Verifying
        $this->assertTrue($this->checkCurrentPage('newsletter_queue'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_put_in_queue_newsletter');
        $this->assertNotNull(
            $this->search(
                array('filter_queue_subject' => $newData['newsletter_template_subject']),
                'newsletter_queue'
            ),
            'Template (Subject:' . $newData['newsletter_template_subject'] . ') is not presented in queue grid'
        );
    }

    /**
     * Admin with Resource: Newsletter can delete newsletter template
     *
     * @param array $loginData
     * @param array $newNewsletter
     *
     * @depends createAdminUser
     * @depends editNewsletterResourceOneRole
     * @depends putNewsToQueueOneRole
     *
     * @test
     * @TestlinkId TL-MAGE-6038
     */
    public function deleteNewsletterOneRole($loginData, $newNewsletter)
    {
        //Data
        $searchData = $this->newsletterHelper()->convertToFilter($newNewsletter);
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('newsletter_templates');
        $this->newsletterHelper()->deleteNewsletter($newNewsletter);
        //Verifying
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_deleted_newsletter');
        $this->assertNull($this->search($searchData, 'newsletter_templates_grid'),
            'Template(Name:' . $newNewsletter['newsletter_template_subject']
                . ') is presented in grid, should be deleted');
        $this->navigate('newsletter_queue');
        $result = $this->search(array('filter_queue_subject' => $newNewsletter['newsletter_template_subject']),
            'newsletter_queue');
        $this->assertNull($result, 'Template (Subject:' . $newNewsletter['newsletter_template_subject']
            . ') is presented in queue grid, should be deleted');
    }

    /**
     * Precondition method for create subscriber
     *
     * @return array
     *
     * @test
     */
    public function preconditionSubscribeCustomer()
    {
        //Create test category for subscribe from frontend
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->reindexInvalidedData();
        $this->flushCache();
        //Subscribe from frontend as guest customer
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
            array('filter_email' => $this->generate('email', 15, 'valid')));
        $this->logoutCustomer();
        $this->categoryHelper()->frontOpenCategory($category['name']);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        $this->assertMessagePresent('success', 'newsletter_success_subscription');
        //Verify that subscriber is presented in subscriber grind
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        //Verifying
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $search),
            'Incorrect status for ' . $search['filter_email'] . ' email');

        return array('filter_email' => $search['filter_email']);
    }

    /**
     * Admin with Resource: Newsletter can unsubscribe/delete subscribers
     *
     * @param $loginData
     * @param $subscriberEmail
     *
     * @depends createAdminUser
     * @depends preconditionSubscribeCustomer
     *
     * @test
     * @TestlinkId TL-MAGE-6039
     */
    public function actionsWithSubscribersOneRole($loginData, $subscriberEmail)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('newsletter_subscribers');
        //Verify that subscriber is present in grid and has status 'subscribed'(For Full newsletter ACL resources admin)
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $subscriberEmail),
            'Incorrect status for ' . $subscriberEmail['filter_email'] . ' email or subscriber is not presented');
        //Change status to unsubscribe
        $this->newsletterHelper()->massAction('unsubscribe', array($subscriberEmail));
        $this->assertMessagePresent('success', 'success_update');
        //Verifying that status is changed
        $this->assertTrue($this->newsletterHelper()->checkStatus('unsubscribed', $subscriberEmail),
            'Incorrect status for ' . $subscriberEmail['filter_email'] . ' email or subscriber is not presented');
        //Delete customers from subscribers list
        $this->newsletterHelper()->massAction('delete', array($subscriberEmail));
        $this->assertMessagePresent('success', 'success_delete');
        $this->assertNull($this->search($subscriberEmail, 'subscribers_grid'),
            'Subscriber ' . $subscriberEmail['filter_email'] . ' still presented in grid, should be deleted');
    }
}
