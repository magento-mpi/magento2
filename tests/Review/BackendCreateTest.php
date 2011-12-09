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
 * Tests for Review verification in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Review_BackendCreateTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     * <p>Login as admin to backend</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Create Simple product</p>
     *
     * @test
     * @return array
     */
    public function createProduct()
    {
        $this->navigate('manage_products');
        $simpleProductData = $this->loadData('simple_product_visible', NULL, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($simpleProductData);
        $this->assertMessagePresent('success', 'success_saved_product');

        return $simpleProductData;
    }

    /**
     * <p>Preconditions:</p>
     * <p>Create Store View</p>
     *
     * @test
     * @return string
     */
    public function createStoreView()
    {
        $this->navigate('manage_stores');
        $storeViewData = $this->loadData('generic_store_view');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');

        return $storeViewData['store_view_name'];
    }

    /**
     * <p>Preconditions:</p>
     * <p>Create Rating</p>
     *
     * @depends createStoreView
     * @test
     * @return string
     */
    public function createRating($storeView)
    {
        $this->navigate('manage_ratings');
        $ratingData = $this->loadData('default_rating', array('visible_in' => $storeView), 'default_value');
        $this->ratingHelper()->createRating($ratingData);
        $this->assertMessagePresent('success', 'success_saved_rating');

        return $ratingData;
    }

    /**
     * <p>Creating a new review</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * @depends createProduct
     * @depends createRating
     * @test
     */
    public function withRequiredFieldsOnly($product, $ratingData)
    {
        $this->navigate('all_reviews');
        $reviewData = $this->loadData('review_required',
                array('filter_sku' => $product['general_sku'],
                    'rating_name' => $ratingData['rating_information']['default_value'],
                    'visible_in' => $ratingData['rating_information']['visible_in']),
                array('nickname', 'summary_of_review', 'review'));
        $this->reviewHelper()->createReview($reviewData);
        $this->assertMessagePresent('success', 'success_saved_review');
    }

    /**
     * <p>Creating a new review when its visible in other Store View with Rating</p>
     * <p>Preconditions:</p>
     * <p>Rating created</p>
     * <p>Store view created</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area - select desired Store View and specify Rating</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * <p>Verification:</p>
     * <p>1. Login to frontend;</p>
     * <p>2. Verify that review is absent in Category and product Page in Default Store View;</p>
     * <p>3. Switch to correct store view;</p>
     * <p>4. Verify review in category;</p>
     * <p>5. Verify review on product page;</p>
     *
     * @depends createProduct
     * @depends createStoreView
     * @depends createRating
     * @test
     */
    public function withRequiredFieldsRatingAndVisibleIn($product, $storeView, $ratingData)
    {
        $this->navigate('all_reviews');
        $reviewData = $this->loadData('review_required',
                array('filter_sku' => $product['general_sku'],
                    'rating_name' => $ratingData['rating_information']['default_value'],
                    'visible_in' => $ratingData['rating_information']['visible_in']),
                array('nickname', 'summary_of_review', 'review'));
        $this->reviewHelper()->createReview($reviewData);
        $this->assertMessagePresent('success', 'success_saved_review');
        $this->reindexInvalidedData();


        $this->frontend();
        $xpath = $this->_getControlXpath('dropdown', 'your_language') . '/option[@selected]';
        $text = trim($this->getText($xpath));
        if (strcmp(trim('Default Store View'), $text) != 0) {
            $this->fillForm(array('your_language' => 'Default Store View'));
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        }
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->addParameter('productId', NULL);
        $this->addParameter('productId', '');
        $this->addParameter('productTitle', $product['general_name']);
        $this->reviewHelper()->defineCorrectParam('first_review', 'productId');
        $this->clickControl('link', 'first_review');
        $this->addParameter('reviewerName', $reviewData['nickname']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'review_details'),
                'Review is on the page, but should not be there');

        $this->frontend();
        $text = trim($this->getText($xpath));
        if (strcmp(trim($storeView), $text) != 0) {
            $this->fillForm(array('your_language' => $storeView));
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        }
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->addParameter('productId', NULL);
        $this->addParameter('productId', '');
        $this->addParameter('productTitle', $product['general_name']);
        $this->reviewHelper()->defineCorrectParam('add_your_review', 'productId');
        $this->clickControl('link', 'add_your_review');
        $this->addParameter('reviewerName', $reviewData['nickname']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'review_details'),
                'Review is not on the page, but should be there');
    }

    /**
     * <p>Empty fields validation</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Leave fields in Review Details area empty</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @dataProvider emptyFields
     * @depends createProduct
     * @depends createRating
     * @test
     */
    public function withRequiredFieldsEmpty($emptyFieldName, $emptyFieldType, $product, $ratingData)
    {
        $this->navigate('all_reviews');
        $reviewData = $this->loadData('review_required',
                array('filter_sku' => $product['general_sku'],
                    'rating_name' => $ratingData['rating_information']['default_value'],
                    'visible_in' => $ratingData['rating_information']['visible_in']),
                array('nickname', 'summary_of_review', 'review'));
        $reviewData[$emptyFieldName] = '%noValue%';
        if ($emptyFieldName == 'visible_in' || $emptyFieldName == 'product_rating') {
            $reviewData['detailed_rating_select'] = '%noValue%';
        }
        $this->reviewHelper()->createReview($reviewData);
        if ($emptyFieldName == 'product_rating') {
            $this->assertMessagePresent('validation', 'empty_validate_rating');
        } else {
            $this->addFieldIdToMessage($emptyFieldType, $emptyFieldName);
            $this->assertMessagePresent('validation', 'empty_required_field');
        }
    }

    public function emptyFields()
    {
        return array(
            array('visible_in', 'multiselect'),
            array('product_rating', 'radiobutton'),
            array('nickname', 'field'),
            array('summary_of_review', 'field'),
            array('review', 'field'),
        );
    }

    /**
     * <p>Creating a new review with long values into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area by long values</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * @depends createProduct
     * @depends createRating
     * @test
     */
    public function withLongValues($product, $ratingData)
    {
        $this->navigate('all_reviews');
        $reviewData = $this->loadData('review_required',
                array('filter_sku' => $product['general_sku'],
                    'rating_name' => $ratingData['rating_information']['default_value'],
                    'visible_in' => $ratingData['rating_information']['visible_in'],
                    'nickname' => $this->generate('string', 255, ':alnum:'),
                    'summary_of_review' => $this->generate('string', 255, ':alnum:'),
                    'review' => $this->generate('string', 255, ':alnum:')));
        $this->reviewHelper()->createReview($reviewData);
        $this->assertMessagePresent('success', 'success_saved_review');
    }

    /**
     * <p>Creating a new review with incorrect length into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area by long values</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * @depends createProduct
     * @depends createRating
     * @test
     */
    public function withIncorrectLengthInRequiredFields($product, $ratingData)
    {
        $this->navigate('all_reviews');
        $reviewData = $this->loadData('review_required',
                array('filter_sku' => $product['general_sku'],
                    'rating_name' => $ratingData['rating_information']['default_value'],
                    'visible_in' => $ratingData['rating_information']['visible_in'],
                    'nickname' => $this->generate('string', 256, ':alnum:'),
                    'summary_of_review' => $this->generate('string', 256, ':alnum:'),
                    'review' => $this->generate('string', 255, ':alnum:')));
        $this->reviewHelper()->createReview($reviewData);
        $this->assertMessagePresent('success', 'success_saved_review');
    }

    /**
     * <p>Creating a new review with special characters into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area by special characters</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * @depends createProduct
     * @depends createRating
     * @test
     */
    public function withSpecialCharacters($product, $ratingData)
    {
        $this->navigate('all_reviews');
        $reviewData = $this->loadData('review_required',
                array('filter_sku' => $product['general_sku'],
                        'rating_name' => $ratingData['rating_information']['default_value'],
                        'visible_in' => $ratingData['rating_information']['visible_in'],
                        'nickname' => $this->generate('string', 32, ':punct:'),
                        'summary_of_review' => $this->generate('string', 32, ':punct:'),
                        'review' => $this->generate('string', 32, ':punct:')));
        $searchData = $this->loadData('search_review',
                array('filter_product_sku' => $product['general_sku'],
                        'filter_nickname' => $reviewData['nickname'],
                        'filter_title' => $reviewData['summary_of_review']));
        $this->reviewHelper()->createReview($reviewData);
        $this->assertMessagePresent('success', 'success_saved_review');
        $this->reviewHelper()->openReview($searchData);
    }

    /**
     * <p> Update review status</p>
     *
     * <p>Preconditions:</p>
     * <p>Review with required fields created and status - "Pending";</p>
     *
     * <p>Steps</p>
     * <p>1. Select created review from the list at "All Reviews" page (by checking checkbox);</p>
     * <p>2. Select in "Actions" - "Update Status";</p>
     * <p>3. Update status to "Approved";</p>
     * <p>4. Click "Submit" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - status updated</p>
     *
     * @depends createProduct
     * @depends createRating
     * @test
     */
    public function changeStatusOfReview($product, $ratingData)
    {
        $this->navigate('all_reviews');
        $reviewData = $this->loadData('review_required',
                array('filter_sku' => $product['general_sku'],
                        'rating_name' => $ratingData['rating_information']['default_value'],
                        'visible_in' => $ratingData['rating_information']['visible_in'],
                        'nickname' => $this->generate('string', 32, ':alnum:'),
                        'summary_of_review' => $this->generate('string', 32, ':alnum:'),
                        'review' => $this->generate('string', 32, ':alnum:'),
                        'status' => 'Pending'));
        $searchData = $this->loadData('search_review',
                array('filter_product_sku' => $product['general_sku'],
                        'filter_nickname' => $reviewData['nickname'],
                        'filter_title' => $reviewData['summary_of_review'],
                        'filter_status' => 'Pending'));
        $this->reviewHelper()->createReview($reviewData);
        $this->assertMessagePresent('success', 'success_saved_review');
        $this->reviewHelper()->editReview(array('status' => 'Approved'), $searchData);
        $this->assertMessagePresent('success', 'success_saved_review');
        $searchData['filter_status'] = 'Approved';
        $this->reviewHelper()->openReview($searchData);
    }

}
