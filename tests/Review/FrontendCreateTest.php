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
 * Reviews Validation on the frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Review_FrontendCreateTest extends Mage_Selenium_TestCase
{

    protected function assertPreConditions()
    {
        $this->addParameter('productUrl', '');
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Customer for tests</p>
     *
     * @test
     */
    public function createCustomer()
    {
        $userData = $this->loadData('customer_account_for_prices_validation', NULL, 'email');
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);

        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Simple Products for tests</p>
     *
     * @test
     */
    public function createProduct()
    {
        $this->navigate('manage_products');
        $simpleProductData = $this->loadData('simple_product_visible', NULL, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($simpleProductData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $simpleProductData;
    }

    /**
     * <p>Review creating with Logged Customer with empty fields</p>
     *
     * <p>1. Login to Frontend</p>
     * <p>2. Open created product</p>
     * <p>3. Add Information to the Review of the product, but with one empty field (via data provider)</p>
     * <p>Expected result:</p>
     * <p>Review is not created. Empty Required Field message appears.</p>
     *
     * @dataProvider emptyFields
     * @depends createCustomer
     * @depends createProduct
     *
     * @test
     */
    public function frontendReviewEmptyFields($emptyFieldName, $emptyFieldType, $customer, $product)
    {
        $reviewData = $this->loadData('frontend_review', array($emptyFieldName => ''));
        $performLogin = array('email' => $customer['email'], 'password' => $customer['password']);
        $this->logoutCustomer();
        $this->clickControl('link', 'log_in');
        $this->fillForm($performLogin);
        $this->clickButton('login');
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->reviewHelper()->frontendAddReview($reviewData);
        $this->addFieldIdToMessage($emptyFieldType, $emptyFieldName);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function emptyFields()
    {
        return array(
            array('nickname', 'field'),
            array('summary_of_your_review', 'field'),
            array('review', 'field')
        );
    }

    /**
     * <p>Review creating with Logged Customer with special characters in fields</p>
     *
     * <p>1. Login to Frontend</p>
     * <p>2. Open created product</p>
     * <p>3. Add Information to the Review of the product, but empty fields</p>
     * <p>Expected result:</p>
     * <p>Review is created. Review can be opened on the backend.</p>
     *
     * @depends createCustomer
     * @depends createProduct
     *
     * @test
     */
    public function frontendReviewSpecialCharacters($customer, $product)
    {
        $reviewData = $this->loadData('frontend_review',
                                      array('nickname'               => $this->generate('string', 32, ':punct:'),
                                            'summary_of_your_review' => $this->generate('string', 32, ':punct:'),
                                            'review'                 => $this->generate('string', 32, ':punct:')));
        $searchData = $this->loadData('search_review',
                                      array('filter_nickname'        => $reviewData['nickname'],
                                            'filter_product_sku'     => $product['general_sku'],
                                            'filter_title'           => $reviewData['summary_of_your_review'],
                                            'filter_review'          => $reviewData['review'],
                                            'filter_type'            => 'Customer',
                                            'filter_status'          => 'Pending'));
        $performLogin = array('email' => $customer['email'], 'password' => $customer['password']);
        $this->logoutCustomer();
        $this->clickControl('link', 'log_in');
        $this->fillForm($performLogin);
        $this->clickButton('login');
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->reviewHelper()->frontendAddReview($reviewData);
        $this->assertTrue($this->successMessage('accepted_review'), $this->messages);
        $this->loginAdminUser();
        $this->navigate('all_reviews');
        $this->reviewHelper()->openReview($searchData);
    }

    /**
     * <p>Review creating with Logged Customer</p>
     *
     * <p>1. Login to Frontend</p>
     * <p>2. Open created product</p>
     * <p>3. Add Review to product</p>
     * <p>4. Check confirmation message</p>
     * <p>5. Goto "My Account"</p>
     * <p>6. Check tag displaying in "My Recent Reviews"</p>
     * <p>7. Goto "My Product Reviews" tab</p>
     * <p>8. Check review displaying on the page</p>
     * <p>9. Open current review - page with assigned product opens</p>
     * <p>Expected result:</p>
     * <p>Review is assigned to correct product</p>
     *
     * @depends createCustomer
     * @depends createProduct
     *
     * @test
     */
    public function frontendReviewVerificationLoggedCustomer($customer, $product)
    {
        $reviewData = $this->loadData('frontend_review', NULL, array('nickname'));
        $searchData = $this->loadData('search_review',
                                      array('filter_nickname'    => $reviewData['nickname'],
                                            'filter_product_sku' => $product['general_sku'],
                                            'filter_type'        => 'Customer',
                                            'filter_status'      => 'Pending'));
        $editReview = array('status' => 'Approved');
        $performLogin = array('email' => $customer['email'], 'password' => $customer['password']);
        $this->logoutCustomer();
        $this->clickControl('link', 'log_in');
        $this->fillForm($performLogin);
        $this->clickButton('login');
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->reviewHelper()->frontendAddReview($reviewData);
        $this->assertTrue($this->successMessage('accepted_review'), $this->messages);
        $this->loginAdminUser();
        $this->navigate('all_reviews');
        $this->reviewHelper()->editReview($editReview, $searchData);
        $this->assertTrue($this->successMessage('success_saved_review'), $this->messages);
        $this->logoutCustomer();
        $this->clickControl('link', 'log_in');
        $this->fillForm($performLogin);
        $this->clickButton('login');
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->reviewHelper()->frontendReviewVerificationInCategory($reviewData, $product['general_name']);
        $this->reviewHelper()->frontendReviewVerificationMyAccount($reviewData['review'],
                                                                   $product['general_name'],TRUE);
    }

    /**
     * <p>Review Verification in Category</p>
     *
     * <p>1. Login to Frontend</p>
     * <p>2. Open created product</p>
     * <p>3. Add Review to product</p>
     * <p>4. Check confirmation message</p>
     * <p>5. Goto "My Account"</p>
     * <p>6. Check review displaying in "My Recent Reviews"</p>
     * <p>7. Goto "My Product Reviews" tab</p>
     * <p>8. Check review displaying on the page</p>
     * <p>9. Open current review - page with assigned product opens</p>
     * <p>Expected result:</p>
     * <p>Review is assigned to correct product</p>
     *
     * @depends createProduct
     *
     * @test
     */
    public function frontendReviewVerificationInCategory($product)
    {
        $reviewData = $this->loadData('frontend_review', NULL, array('nickname'));
        $searchData = $this->loadData('search_review',
                                      array('filter_nickname'    => $reviewData['nickname'],
                                            'filter_product_sku' => $product['general_sku'],
                                            'filter_type'        => 'Guest',
                                            'filter_status'      => 'Pending'));
        $editReview = array('status' => 'Approved');
        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->reviewHelper()->frontendAddReview($reviewData);
        $this->assertTrue($this->successMessage('accepted_review'), $this->messages);
        $this->loginAdminUser();
        $this->navigate('all_reviews');
        $this->reviewHelper()->editReview($editReview, $searchData);
        $this->assertTrue($this->successMessage('success_saved_review'), $this->messages);
        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->reviewHelper()->frontendReviewVerificationInCategory($reviewData, $product['general_name']);
    }

    /**
     * Review creating with Not Logged Customer
     *
     * <p>1. Goto Frontend</p>
     * <p>2. Open created product</p>
     * <p>3. Add Review to product</p>
     * <p>4. Customer is able to create and submit review</p>
     * <p>Expected result:</p>
     * <p>Success message appears - "Your review has been accepted for moderation."</p>
     *
     * <p>Verification:</p>
     * <p>1. Login to backend;</p>
     * <p>2. Navigate to Catalog -> Reviews and Ratings -> Customer Reviews -> Pending Reviews;</p>
     * <p>Expected result:</p>
     * <p>Review is present into the list and has type - "Guest";</p>
     *
     * @depends createProduct
     *
     * @test
     */
    public function frontendReviewVerificationNotLoggedCustomer($product)
    {
        $reviewData = $this->loadData('frontend_review', NULL, array('nickname'));
        $searchData = $this->loadData('search_review',
                                      array('filter_nickname'    => $reviewData['nickname'],
                                            'filter_product_sku' => $product['general_sku'],
                                            'filter_type'        => 'Guest',
                                            'filter_status'      => 'Pending'));
        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->reviewHelper()->frontendAddReview($reviewData);
        $this->assertTrue($this->successMessage('accepted_review'), $this->messages);
        $this->loginAdminUser();
        $this->navigate('all_reviews');
        $this->reviewHelper()->openReview($searchData);
    }
}
