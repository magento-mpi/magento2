<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter Subscription validation
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Newsletter_FrontendCreateTest extends Mage_Selenium_TestCase {

    protected function assertPreConditions() {
        $this->addParameter('productUrl', '');
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Customer for tests</p>
     *
     * @test
     */
    public function createCustomer() {
        //Data
        $userData = $this->loadData('customer_account_for_prices_validation', NULL, 'email');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);

        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category to use during tests</p>
     *
     * @test
     */
    public function createCategory() {
        $this->loginAdminUser();
        $this->navigate('manage_categories');
        $this->categoryHelper()->checkCategoriesPage();
        $rootCat = 'Default Category';
        $categoryData = $this->loadData('sub_category_required', null, 'name');
        $this->categoryHelper()->createSubCategory($rootCat, $categoryData);
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->categoryHelper()->checkCategoriesPage();

        return $rootCat . '/' . $categoryData['name'];
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Simple Products for tests</p>
     *
     * @depends createCategory
     * @test
     */
    public function createProduct($category) {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $simpleProductData = $this->loadData('simple_product_for_prices_validation_front_1', array('categories' => $category), array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($simpleProductData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        return $simpleProductData['general_name'];
    }

    /**
     * <p>With valid email</p>
     *
     * <p>1. Login to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter valid email to subscribe</p>
     * <p>4. Check message</p>
     * <p>5. Login to backend</p>
     * <p>6. Goto Newsletter -> Newsletter Subscribers</p>
     * <p>7. Verify email in subscribers list</p>
     *
     * <p>Expected result: email is present into the subscribers list</p>
     *
     * @depends createCustomer
     * @depends createCategory
     * @depends createProduct
     *
     * @test
     */
    public function subscriberEmailVerificationInvalidAddress($customer, $category) {
        //Data
        $newSubscriberEmail = $this->generate('email', 15, 'invalid');
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($customer);
        $nodes = explode('/', $category);
        $category = end($nodes);
        $this->categoryHelper()->frontOpenCategory($category);
        $this->fillForm(array('sign_up_newsletter' => $newSubscriberEmail));
        $this->clickButton('subscribe', FALSE);
        $this->assertTrue($this->validationMessage('invalid_email'), $this->messages);
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        $this->assertEquals(NULL, $this->search(array('filter_email' => $newSubscriberEmail)));
    }

    /**
     * <p>With valid email</p>
     *
     * <p>1. Login to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter valid email to subscribe</p>
     * <p>4. Check message</p>
     * <p>5. Login to backend</p>
     * <p>6. Goto Newsletter -> Newsletter Subscribers</p>
     * <p>7. Verify email in subscribers list</p>
     *
     * <p>Expected result: email is present into the subscribers list</p>
     *
     * @dataProvider newsletterData
     * @depends createCustomer
     * @depends createCategory
     * @depends createProduct
     *
     * @test
     */
    public function subscriberEmailVerificationValidAddress($newSubscriberEmail, $customer, $category) {
        //Data
        $nodes = explode('/', $category);
        $category = end($nodes);
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->categoryHelper()->frontOpenCategory($category);
        $this->fillForm(array('sign_up_newsletter' => $newSubscriberEmail));
        $this->clickButton('subscribe');
        $this->assertTrue($this->successMessage('success_subscription'), $this->messages);
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        $this->assertNotEquals(NULL, $this->search(array('filter_email' => $newSubscriberEmail)));
    }

    public function newsletterData() {
        return array(
            array($this->generate('email', 15, 'valid')),
            array($this->generate('email', 70, 'valid'))
        );
    }

    /**
     * <p>With long valid email (> 70 characters)</p>
     *
     * <p>1. Login to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter valid email to subscribe</p>
     * <p>4. Check message</p>
     * <p>5. Login to backend</p>
     * <p>6. Goto Newsletter -> Newsletter Subscribers</p>
     * <p>7. Verify email in subscribers list</p>
     *
     * <p>Expected result: email is absent into the subscribers list</p>
     *
     * @depends createCustomer
     * @depends createCategory
     * @depends createProduct
     *
     * @test
     */
    public function subscriberEmailVerificationValidLong($customer, $category) {
        //Data
        $newSubscriberEmail = $this->generate('email', 300, 'valid');
        $nodes = explode('/', $category);
        $category = end($nodes);
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->categoryHelper()->frontOpenCategory($category);
        $this->fillForm(array('sign_up_newsletter' => $newSubscriberEmail));
        $this->clickButton('subscribe');
        $this->assertTrue($this->errorMessage('long_email'), $this->messages);
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        $this->assertEquals(NULL, $this->search(array('filter_email' => $newSubscriberEmail)));
    }

    /**
     * <p>With empty email field</p>
     *
     * <p>1. Login to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Leave email field empty</p>
     * <p>4. Check validation message</p>
     * <p>5. Login to backend</p>
     * <p>6. Goto Newsletter -> Newsletter Subscribers</p>
     * <p>7. Verify email in subscribers list</p>
     *
     * <p>Expected result: email is absent into the subscribers list</p>
     *
     * @depends createCustomer
     * @depends createCategory
     * @depends createProduct
     *
     * @test
     */
    public function subscriberEmailVerificationEmptyField($customer, $category) {
        //Data
        $newSubscriberEmail = $this->generate('email', NULL, 'valid');
        $nodes = explode('/', $category);
        $category = end($nodes);
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->categoryHelper()->frontOpenCategory($category);
        $this->fillForm(array('sign_up_newsletter' => $newSubscriberEmail));
        $this->clickButton('subscribe', FALSE);
        $this->assertTrue($this->validationMessage('reqired_field'), $this->messages);
    }

    /**
     * <p> Delete Subscriber</p>
     *
     * <p> Steps:</p>
     * <p>1. Login to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter email to subscribe</p>
     * <p>4. Check message</p>
     * <p>5. Login to backend</p>
     * <p>6. Goto Newsletter -> Newsletter Subscribers</p>
     * <p>7. Verify email in subscribers list</p>
     * <p>8. Select subscriber`s email from the list</p>
     * <p>9. Choose "Delete" option in actions</p>
     * <p>10. Click "Submit" button</p>
     * <p>11. Check confirmation message</p>
     *
     * <p>Expected result: Subscriber`s email removed from the list</p>
     *
     * @depends createCustomer
     * @depends createCategory
     * @depends createProduct
     *
     * @test
     */
    public function subscriberDelete($customer, $category) {
        //Data
        $this->addParameter('qtyOfRecords', 1);
        $nodes = explode('/', $category);
        $category = end($nodes);
        $newSubscriberEmail = $this->generate('email', 15, 'valid');
        //Steps
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->categoryHelper()->frontOpenCategory($category);
        $this->fillForm(array('sign_up_newsletter' => $newSubscriberEmail));
        $this->clickButton('subscribe');
        //Verification
        $this->assertTrue($this->successMessage('success_subscription'), $this->messages);
        //Steps
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        $this->searchAndChoose(array('filter_email' => $newSubscriberEmail));
        $this->fillForm(array('subscribers_massaction' => 'Delete'));
        $this->clickButton('submit');
        //Verification
        $this->assertTrue($this->successMessage('success_delete'), $this->messages);
    }

    /**
     * <p> Unsubscribe Subscriber</p>
     *
     * <p> Steps:</p>
     * <p>1. Login to Frontend</p>
     * <p>2. Open created category</p>
     * <p>3. Enter email to subscribe</p>
     * <p>4. Check message</p>
     * <p>5. Login to backend</p>
     * <p>6. Goto Newsletter -> Newsletter Subscribers</p>
     * <p>7. Verify email in subscribers list</p>
     * <p>8. Select subscriber`s email from the list</p>
     * <p>9. Choose "Unsubscribe" option in actions</p>
     * <p>10. Click "Submit" button</p>
     * <p>11. Check confirmation message</p>
     *
     * <p>Expected result: Subscriber`s email status changed</p>
     *
     * @depends createCustomer
     * @depends createCategory
     * @depends createProduct
     *
     * @test
     */
    public function subscriberUnsubscribe($customer, $category) {
        //Data
        $this->addParameter('qtyOfRecords', 1);
        $nodes = explode('/', $category);
        $category = end($nodes);
        $newSubscriberEmail = $this->generate('email', 15, 'valid');
        //Steps
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->categoryHelper()->frontOpenCategory($category);
        $this->fillForm(array('sign_up_newsletter' => $newSubscriberEmail));
        $this->clickButton('subscribe');
        //Verification
        $this->assertTrue($this->successMessage('success_subscription'), $this->messages);
        //Steps
        $this->loginAdminUser();
        $this->navigate('newsletter_subscribers');
        $this->searchAndChoose(array('filter_email' => $newSubscriberEmail));
        $this->fillForm(array('subscribers_massaction' => 'Unsubscribe'));
        $this->clickButton('submit');
        //Verification
        $this->assertTrue($this->successMessage('success_update'), $this->messages);
    }

}
