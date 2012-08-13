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
 */
class Community2_Mage_ACL_NewsletterResourceOneRoleTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->admin('log_in_to_admin', false);
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Create Admin User with full Newsletter resources role</p>
     *
     *
     * @return array
     * @test
     */
    public function createAdminUser()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource =
            $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom', array('resource_1' => 'Newsletter'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);

        return $loginData;
    }

    /**
     * <p>Admin with Resource: Newsletter has access to Newsletter menu. All necessary buttons are presented</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Click "Add New Template"</p>
     * <p>Expected results:</p>
     * <p>1. Navigation menu has only 1 parent element(Newsletter)</p>
     * <p>2. Navigation menu(Newsletter)  has only 4 child elements</p>
     * <p>3. Opened page is "New Newsletter Template" </p>
     * <p>4. Buttons 'Back','Reset','Preview Template','Save Template' are presented on page</p>
     *
     * @param array $loginData
     *
     * @depends  createAdminUser
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6034
     */
    public function verifyScopeNewsletterResource($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        // Verify that navigation menu has only 1 parent element
        $this->assertEquals('1', count($this->getElementsByXpath(
                $this->_getControlXpath('pageelement', 'navigation_menu_items'))),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 4 child elements
        $this->assertEquals('4', count($this->getElementsByXpath(
                $this->_getControlXpath('pageelement', 'navigation_children_menu_items'))),
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
     * <p>Admin with Resource: Newsletter can create new newsletter template</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Click "Add New Template"</p>
     * <p>3. Fill all fields and click "Save Template" button</p>
     * <p>Expected results:</p>
     * <p>1. Opened page is "Newsletter Templates" </p>
     * <p>2. Success Message is appered "The newsletter template has been saved."</p>
     * <p>3. Templates grid contains created template </p>
     *
     * @param $loginData
     *
     * @depends createAdminUser
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6035
     */
    public function createNewsletterResourceOneRole($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('newsletter_templates');
        $newsData = $this->loadDataSet('Newsletter', 'generic_newsletter_data');
        $this->newsletterHelper()->createNewsletterTemplate($newsData);
        $this->validatePage('newsletter_templates');
        //$this->assertMessagePresent('success', 'success_save_newsletter');
        $searchData = $this->newsletterHelper()->convertToFilter($newsData);
        $this->assertNotNull($this->search($searchData),
            'Template( Name: ' . $newsData['newsletter_template_name'] . ' ) is not presented in grid');

        return $newsData;
    }

    /**
     *<p>Admin with Resource: Newsletter can edit and save exists newsletter template</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Find test template in Newsletter Template grid and click</p>
     * <p>3. Fill all fields with new data and click "Save Template" button</p>
     * <p>Expected results:</p>
     * <p>1. Opened page is "Newsletter Templates" </p>
     * <p>2. Success Message is appered "The newsletter template has been saved."</p>
     * <p>3. Templates grid contains created template </p>
     *
     * @param array $loginData
     * @param array $newsData
     *
     * @depends createAdminUser
     * @depends createNewsletterResourceOneRole
     *
     * @return array $newNewsletterData
     * @test
     * @TestlinkId TL-MAGE-6036
     */
    public function editNewsletterResourceOneRole($loginData, $newsData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $newNewsletterData = $this->loadDataSet('Newsletter', 'edit_newsletter');
        $this->newsletterHelper()->editNewsletter($newsData, $newNewsletterData);
        $this->validatePage('newsletter_templates');
       // $this->assertMessagePresent('success', 'success_save_newsletter');
        $searchData = $this->newsletterHelper()->convertToFilter($newNewsletterData);
        $this->assertNotNull($this->search($searchData),
            'Template (Name: ' . $newNewsletterData['newsletter_template_name'] . ') is not presented in grid');

        return $newNewsletterData;
    }

    /**
     * <p>Admin with Resource: Newsletter can put newsletter template to queue<p/>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Find test template in Newsletter Template grid</p>
     * <p>3. In Action column find dropdown and select "Queue Newsletter..."</p>
     * <p>4. Select/fill Queue Date Start</p>
     * <p>5. Fill all fields with new data</p>
     * <p>6. Click "Save Newsletter" button</p>
     * <p>Expected results:</p>
     * <p>1. Opened page is "Newsletter Queue" </p>
     * <p>2. Success Message is appered "The newsletter Queue has been saved."</p>
     * <p>3. Queue grid contains created template </p>
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
        $this->adminUserHelper()->loginAdmin($loginData);
        $newData = $this->loadDataSet('Newsletter', 'edit_newsletter_before_queue',
            array('newsletter_queue_data' => '12.12.12'));
        $this->newsletterHelper()->putNewsToQueue($newNewsletterData, $newData);
        $this->validatePage('newsletter_queue');
        //$this->assertMessagePresent('success', 'success_put_in_queue_newsletter');
        $this->assertNotNull($this->search(array('filter_queue_subject'=> $newData['newsletter_template_subject'])),
            'Template (Subject:' . $newData['newsletter_template_subject'] . ') is not presented in queue grid');
    }

    /**
     * <p>Admin with Resource: Newsletter can delete newsletter template</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Find test teplate in Newsletter Template grid and click</p>
     * <p>3. Click "Delete Template"</p>
     * <p>4. Go to Newsletter>Newsletter queue</p>
     * <p>Expected results:</p>
     * <p>1. Opened page is "Newsletter Templates" </p>
     * <p>2. Success Message is appered "The newsletter template has been deleted."</p>
     * <p>3. Templates grid does not contain created template </p>
     * <p>4. Queue grid does not contain created template </p>
     *
     * @param array $loginData
     * @param array  $newNewsletterData
     *
     * @depends createAdminUser
     * @depends editNewsletterResourceOneRole
     * @depends putNewsToQueueOneRole
     *
     * @test
     * @TestlinkId TL-MAGE-6038
     */
    public function deleteNewsletterOneRole($loginData, $newNewsletterData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->newsletterHelper()->deleteNewsletter($newNewsletterData);
        $this->validatePage('newsletter_templates');
        //$this->assertMessagePresent('success', 'success_delete_newsletter');
        $searchData = $this->newsletterHelper()->convertToFilter($newNewsletterData);
        $this->assertNull($this->search($searchData),
            'Template(Name:' . $newNewsletterData['newsletter_template_subject']
            . ') is presented in grid, should be deleted');
        $this->navigate('newsletter_queue');
        $this->assertNull($this->search(array('filter_queue_subject'=>
                                              $newNewsletterData['newsletter_template_subject'])),
            'Template (Subject:' . $newNewsletterData['newsletter_template_subject']
            . ') is presented in queue grid, should be deleted');
    }

    /**
     * <p>Precondition method for create subscriber</p>
     *
     * @return array
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

        return array('filter_email'=> $search['filter_email']);
    }

    /**
     * <p>Admin with Resource: Newsletter can unsubscribe/delete subscribers</p>
     *
     * <p>Steps:</p>
     * <p>1. Login to backend as test admin user</p>
     * <p>2. Go to Newsletter>Newsletter Subscribers</p>
     * <p>3. Find in grid test subscribed customer and select(checkobx)</p>
     * <p>4. In Mass action dropdown select "Unsubscribe", click "Submit" </p>
     * <p>5. In Mass action dropdown select "Delete", click "Submit" </p>
     * <p>Expected results:</p>
     * <p>1. after step 4: Opened page is "Newsletter Subscribers" </p>
     * <p>2. after step 4: Success Message is appered "Total of 1 record(s) were updated"</p>
     * <p>3. after step 4: Subscribers grid contains this user with status = Unsubscribe </p>
     * <p>4. after step 5: Opened page is "Newsletter Subscribers" </p>
     * <p>5. after step 5: Success Message is appered "Total of 1 record(s) were updated"</p>
     * <p>6. after step 5: Subscribers grid does not contain this user</p>
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
        //Verifying that subscriber is presented in grid and has status 'subscribed' (For Full newsletter ACL resources admin)
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
        $this->assertNull($this->search($subscriberEmail),
            'Subscriber ' . $subscriberEmail['filter_email'] . ' still presented in grid, should be deleted');
    }
}
