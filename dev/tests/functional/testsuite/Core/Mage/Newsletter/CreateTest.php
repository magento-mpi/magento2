<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter Subscription validation
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Newsletter_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->logoutCustomer();
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category to use during tests</p>
     *
     * @return string $category
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->reindexInvalidedData();
        $this->flushCache();
        return $category['name'];
    }

    /**
     * <p>With valid email</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3250
     */
    public function guestUseValidNotExistCustomerEmail($category)
    {
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
            array('filter_email' => $this->generate('email', 15, 'valid')));
        //Steps
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('success', 'newsletter_success_subscription');
        //Steps
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        //Verifying
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $search),
            'Incorrect status for ' . $search['filter_email'] . ' email');
    }

    /**
     * <p>With valid email that used for registered customer</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3239
     */
    public function guestUseValidExistCustomerEmail($category)
    {
        $customer = $this->loadDataSet('Customers', 'generic_customer_account');
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
            array('filter_email' => $customer['email']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($customer);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('error', 'newsletter_email_used');
    }

    /**
     * <p>With invalid email</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3249
     */
    public function guestInvalidEmail($category)
    {
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
            array('filter_email' => $this->generate('email', 15, 'invalid')));
        //Steps
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('validation', 'newsletter_required_field');
    }

    /**
     * <p>With empty email field</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3248
     */
    public function guestEmptyEmail($category)
    {
        //Steps
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe('');
        //Verifying
        $this->assertMessagePresent('validation', 'newsletter_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>With long valid email</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3251
     */
    public function guestLongValidEmail($category)
    {
        //Steps
        $newSubscriberEmail = $this->generate('email', 250, 'valid');
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($newSubscriberEmail);
        //Verify
        $this->assertMessagePresent('error', 'newsletter_long_email');
    }

    /**
     * subscribe registered customer email.
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5399
     */
    public function customerUseOwnEmail($category)
    {
        $customer = $this->loadDataSet('Customers', 'customer_account_register');
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
            array('filter_email' => $customer['email']));
        //Steps
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($customer);
        $this->assertMessagePresent('success', 'success_registration');
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('success', 'newsletter_success_subscription');
        //Steps
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        //Verifying
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $search),
            'Incorrect status for ' . $search['filter_email'] . ' email');
    }

    /**
     * <p> Delete Subscriber</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3247
     */
    public function deleteSubscriber($category)
    {
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
            array('filter_email' => $this->generate('email', 15, 'valid')));
        //Steps
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('success', 'newsletter_success_subscription');
        //Steps
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        //Verifying
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $search),
            'Incorrect status for ' . $search['filter_email'] . ' email');
        //Steps
        $this->newsletterHelper()->massAction('delete', array($search));
        //Verifying
        $this->assertMessagePresent('success', 'success_delete');
        $this->assertNull($this->search($search, 'subscribers_grid'), 'Subscriber is not deleted');
    }

    /**
     * <p>Unsubscribe Subscriber</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3252
     */
    public function subscriberUnsubscribe($category)
    {
        $search = $this->loadDataSet('Newsletter', 'search_newsletter_subscribers',
            array('filter_email' => $this->generate('email', 15, 'valid')));
        //Steps
        $this->categoryHelper()->frontOpenCategory($category);
        $this->newsletterHelper()->frontSubscribe($search['filter_email']);
        //Verifying
        $this->assertMessagePresent('success', 'newsletter_success_subscription');
        //Steps
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        //Verifying
        $this->assertTrue($this->newsletterHelper()->checkStatus('subscribed', $search),
            'Incorrect status for ' . $search['filter_email'] . ' email');
        //Steps
        $this->newsletterHelper()->massAction('unsubscribe', array($search));
        //Verifying
        $this->assertMessagePresent('success', 'success_update');
        $this->assertTrue($this->newsletterHelper()->checkStatus('unsubscribed', $search),
            $this->getParsedMessages());
    }
}