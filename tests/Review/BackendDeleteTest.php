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
 * Delete review into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Review_BackendDeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Login as admin to backend</p>
     * <p>Navigate to Catalog -> Reviews and Ratings -> Customer Reviews -> All Reviews</p>
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
        $this->loginAdminUser();
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
        $this->assertTrue($this->successMessage('success_saved_rating'), $this->messages);

        return $ratingData;
    }

    /**
     * <p>Delete review with Rating created</p>
     *
     * <p>Preconditions:</p>
     * <p>Review with rating created;</p>
     * <p>Rating created</p>
     * <p>Steps:</p>
     * <p>1. Select created review from the list and open it;</p>
     * <p>2. Click "Delete Review" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - review removed from the list</p>
     *
     * @depends createProduct
     * @depends createRating
     *
     * @test
     */
    public function deleteWithRating($product, $ratingData)
    {
        $this->navigate('all_reviews');
        $reviewData = $this->loadData('review_required',
                                      array ('filter_sku' =>  $product['general_sku'],
                                            'rating_name' => $ratingData['rating_information']['default_value'],
                                            'visible_in' => $ratingData['rating_information']['visible_in']),
                                      array('nickname', 'summary_of_review', 'review'));
        $searchData = $this->loadData('search_review', array('filter_product_sku' => $product['general_sku'],
                                                            'filter_nickname' => $reviewData['nickname'],
                                                            'filter_title' => $reviewData['summary_of_review']));
        $this->reviewHelper()->createReview($reviewData);
        $this->assertTrue($this->successMessage('success_saved_review'), $this->messages);
        $this->reviewHelper()->deleteReview($searchData);
        $this->assertTrue($this->successMessage('success_deleted_review'), $this->messages);
    }

    /**
     * <p>Delete review using Mass-Action</p>
     *
     * <p>Preconditions:</p>
     * <p>Review created;</p>
     * <p>Steps:</p>
     * <p>1. Select created review from the list check it;</p>
     * <p>2. Select "Delete" in Actions;</p>
     * <p>3. Click "Submit" button;</p>
     * <p>Success message appears - review removed from the list</p>
     *
     * @depends createProduct
     * @depends createRating
     * 
     * @test
     */
    public function deleteMassAction($product, $ratingData)
    {
        $this->navigate('all_reviews');
        $reviewData = $this->loadData('review_required',
                                      array ('filter_sku' =>  $product['general_sku'],
                                            'rating_name' => $ratingData['rating_information']['default_value'],
                                            'visible_in' => $ratingData['rating_information']['visible_in']),
                                      array('nickname', 'summary_of_review', 'review'));
        $searchData = $this->loadData('search_review', array('filter_product_sku' => $product['general_sku'],
                                                            'filter_nickname' => $reviewData['nickname'],
                                                            'filter_title' => $reviewData['summary_of_review']));
        $this->reviewHelper()->createReview($reviewData);
        $this->assertTrue($this->successMessage('success_saved_review'), $this->messages);
        $this->searchAndChoose($searchData);
        $this->fillForm(array('actions' => 'Delete'));
        $this->clickButtonAndConfirm('submit', 'confirmation_for_delete_all');
        $this->assertTrue($this->successMessage('success_deleted_review_massaction'), $this->messages);
    }
}
