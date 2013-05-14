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
class Core_Mage_Acl_NewsletterResourceDifferentRolesTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Admin with Resource: Newsletter/Newsletter Templates has ability to create new newsletter template</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6066
     */
    public function createNewsletterResourceDifferentRoles()
    {
        //Preconditions
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-communications-newsletter_template'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //Create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
        $newsData = $this->loadDataSet('Newsletter', 'generic_newsletter_data');
        $this->newsletterHelper()->createNewsletterTemplate($newsData);
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_saved_newsletter');
        $searchData = $this->newsletterHelper()->convertToFilter($newsData);
        $this->assertNotNull($this->search($searchData, 'newsletter_templates_grid'),
            'Template( Name: ' . $newsData['newsletter_template_name'] . ' ) is not presented in grid');

        return $newsData;
    }

    /**
     * <p>Admin with Resource: Newsletter/Newsletter Templates has ability to edit/save exists newsletter template</p>
     *
     * @param array $newsData
     *
     * @return array
     *
     * @test
     * @depends createNewsletterResourceDifferentRoles
     * @TestlinkId TL-MAGE-6067
     */
    public function editNewsletterResourceDifferentRoles($newsData)
    {
        $this->markTestIncomplete('MAGETWO-8369');
        //Preconditions
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-communications-newsletter_template'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //Create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
        $newNewsletterData = $this->loadDataSet('Newsletter', 'edit_newsletter');
        $this->newsletterHelper()->editNewsletter($newsData, $newNewsletterData);
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
        //$this->assertMessagePresent('success', 'success_save_newsletter');
        $searchData = $this->newsletterHelper()->convertToFilter($newNewsletterData);
        $this->assertNotNull($this->search($searchData, 'newsletter_templates_grid'),
            'Template (Name: ' . $newNewsletterData['newsletter_template_name'] . ') is not presented in grid');

        return $newNewsletterData;
    }

    /**
     * <p>Admin with Resource: Newsletter/Newsletter Templates</p>
     * <p>And Newsletter Queue has ability to put newsletter template to queue</p>
     *
     * @param $newNewsletterData
     *
     * @test
     * @depends editNewsletterResourceDifferentRoles
     * @TestlinkId TL-MAGE-6068
     */
    public function putNewsToQueueDifferentRoles($newNewsletterData)
    {
        //Preconditions
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl', array('resource_acl' => array(
            'marketing-communications-newsletter_template',
            'marketing-communications-newsletter_queue'
        )));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //Create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
        $newData = $this->loadDataSet('Newsletter', 'edit_newsletter_before_queue',
            array('newsletter_queue_data' => '12.12.12'));
        $this->newsletterHelper()->putNewsToQueue($newNewsletterData, $newData);
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
     * <p>Admin with Resource: Newsletter/Newsletter Templates</p>
     * <p>And Newsletter Queue has ability to delete newsletter template</p>
     *
     * @param $newNewsletter
     *
     * @test
     * @depends  editNewsletterResourceDifferentRoles
     * @TestlinkId TL-MAGE-6069
     */

    public function deleteNewsletterOneRole($newNewsletter)
    {
        //Preconditions
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl', array('resource_acl' => array(
            'marketing-communications-newsletter_template',
            'marketing-communications-newsletter_queue'
        )));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //Create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
        $this->newsletterHelper()->deleteNewsletter($newNewsletter);
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
        //$this->assertMessagePresent('success', 'success_delete_newsletter');
        $searchData = $this->newsletterHelper()->convertToFilter($newNewsletter);
        $this->assertNull($this->search($searchData, 'newsletter_templates_grid'),
            'Template(Name:' . $newNewsletter['newsletter_template_subject']
                . ') is presented in grid, should be deleted');
        $this->navigate('newsletter_queue');
        $result = $this->search(array('filter_queue_subject' => $newNewsletter['newsletter_template_subject']),
            'newsletter_queue_grid');
        $this->assertNull($result, 'Template (Subject:' . $newNewsletter['newsletter_template_subject']
            . ') is presented in queue grid, should be deleted');
    }

    /**
     * <p>Precondition method for create subscriber</p>
     *
     * @return array
     *
     * @test
     */
    public function preconditionSubscribeCustomer()
    {
        //Create test category for subscribe from frontend
        $category = $this->loadDataSet('Category', 'sub_category_required');
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
        //Verify that subscriber is presented in subscriber grid
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        //Verifying
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $search),
            'Incorrect status for ' . $search['filter_email'] . ' email');

        return array('filter_email' => $search['filter_email']);
    }

    /**
     * <p>Admin with Resource: Newsletter/Newsletter Subscribers  has ability to unsubscribe/delete subscribers</p>
     *
     * @param $subscriberEmail
     *
     * @test
     * @depends preconditionSubscribeCustomer
     * @TestlinkId TL-MAGE-6070
     */
    public function actionsWithSubscribersOneRole($subscriberEmail)
    {
        //Preconditions
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-communications-newsletter_subscribers'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //Create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('newsletter_templates'), $this->getParsedMessages());
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
