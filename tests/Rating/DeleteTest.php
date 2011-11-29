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
 * Delete Rating in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Rating_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Login as admin to backend</p>
     * <p>Navigate to Catalog -> Reviews and Ratings -> Manage Ratings</p>
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
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

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
        $this->assertTrue($this->successMessage('success_saved_store_view'), $this->messages);

        return $storeViewData['store_view_name'];
    }

    /**
     * <p>Delete rating that is used in Review</p>
     * <p>Preconditions:</p>
     * <p>Rating created</p>
     * <p>Review created using Rating</p>
     * <p>Steps:</p>
     * <p>1. Open created rating;</p>
     * <p>2. Click "Delete" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - Rating removed from the list</p>
     *
     * <p>Verification:</p>
     * <p>1. Navigate to Catalog -> Reviews and Ratings -> Customer Reviews -> All Reviews;</p>
     * <p>2. Select created Review from the list and open it;</p>
     * <p>3. Verify that Rating is absent in review;</p>
     *
     * @depends createProduct
     * @depends createStoreView
     * @test
     */
    public function deleteRatingUsedInReview($product, $storeView)
    {
        $ratingData = $this->loadData('default_rating', array('visible_in' => $storeView), 'default_value');
        $searchData = $this->loadData('search_rating',
                                      array('filter_rating_name' =>$ratingData['rating_information']['default_value']));
        $reviewData = $this->loadData('review_required',
                                      array ('filter_sku' =>  $product['general_sku'],
                                            'rating_name' => $ratingData['rating_information']['default_value'],
                                            'visible_in' => $ratingData['rating_information']['visible_in']),
                                      array ('nickname', 'summary_of_review', 'review'));
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($ratingData);
        $this->assertTrue($this->successMessage('success_saved_rating'), $this->messages);
        
        $this->navigate('all_reviews');
        $this->reviewHelper()->createReview($reviewData);
        $this->assertTrue($this->successMessage('success_saved_review'), $this->messages);
        $this->reindexInvalidedData();

        $this->frontend();
        $xpath = $this->_getControlXpath('dropdown', 'your_language') . '/option[@selected]';
        $text = trim($this->getText($xpath));
        if (strcmp(trim($storeView), $text) != 0) {
            $this->fillForm(array('your_language' => $storeView));
            $this->waitForPageToLoad();
        }
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->addParameter('productId', NULL);
        $this->addParameter('productId', '');
        $this->addParameter('productTitle', $product['general_name']);
        $this->reviewHelper()->defineCorrectParam('add_your_review', 'productId');
        $this->clickControl('link', 'add_your_review');
        $this->addParameter('rateName', $ratingData['rating_information']['default_value']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'review_table_rate_name'),
                           'Rating is not on the page, but should be there');

        $this->loginAdminUser();
        $this->navigate('manage_ratings');
        $this->ratingHelper()->deleteRating($searchData);
        $this->reindexInvalidedData();

        $this->frontend();
        $text = trim($this->getText($xpath));
        if (strcmp(trim($storeView), $text) != 0) {
            $this->fillForm(array('your_language' => $storeView));
            $this->waitForPageToLoad();
        }
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->addParameter('productId', NULL);
        $this->addParameter('productId', '');
        $this->addParameter('productTitle', $product['general_name']);
        $this->reviewHelper()->defineCorrectParam('add_your_review', 'productId');
        $this->clickControl('link', 'add_your_review');
        $this->addParameter('rateName', $ratingData['rating_information']['default_value']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'review_table_rate_name'),
                           'Rating is on the page, but should not be there');
    }

    /**
     * <p>Delete rating</p>
     * <p>Preconditions:</p>
     * <p>Rating created</p>
     * <p>Steps:</p>
     * <p>1. Open created rating;</p>
     * <p>2. Click "Delete" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - Rating removed from the list</p>
     *
     * @test
     */
    public function deleteRatingNotUsedInReview()
    {
        $ratingData = $this->loadData('rating_required_fields', NULL, 'default_value');
        $searchData = $this->loadData('search_rating',
                                      array('filter_rating_name' =>$ratingData['rating_information']['default_value']));
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($ratingData);
        $this->assertTrue($this->successMessage('success_saved_rating'), $this->messages);
        $this->ratingHelper()->deleteRating($searchData);
    }

}
