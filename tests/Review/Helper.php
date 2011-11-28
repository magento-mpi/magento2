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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Review_Helper extends Mage_Selenium_TestCase
{

    /**
     * Creates review
     *
     * @param $reviewData
     */
    public function createReview($reviewData)
    {
        $this->clickButton('add_new_review');
        $this->fillInfo($reviewData);
        $this->saveForm('save_review');
    }

    /**
     * Edit existing review
     *
     * @param $reviewData
     * @param $searchData
     */
    public function editReview($reviewData, $searchData)
    {
        $this->openReview($searchData);
        $this->fillInfo($reviewData);
        $this->saveForm('save_review');
    }

    /**
     * Opens review
     *
     * @param array $reviewSearch
     */
    public function openReview(array $reviewSearch)
    {
        $reviewSearch = $this->arrayEmptyClear($reviewSearch);
        $xpathTR = $this->search($reviewSearch, 'all_reviews_grid');
        $this->assertNotEquals(null, $xpathTR, 'Review is not found');
        $this->click($xpathTR . "//a[text()='Edit']");
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->validatePage($this->_findCurrentPageFromUrl($this->getLocation()));
    }

    /**
     * Opens product to review
     *
     * @param array $productSearch
     */
    public function openProduct(array $productSearch)
    {
        $productSearch = $this->arrayEmptyClear($productSearch);
        $xpathTR = $this->search($productSearch, 'select_product_grid');
        $this->assertNotEquals(null, $xpathTR, 'Product is not found');
        $this->click($xpathTR);
        $this->pleaseWait();
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->validatePage($this->_findCurrentPageFromUrl($this->getLocation()));
    }

    /**
     * Fills tabs in new/edit review
     *
     * @param string|array $reviewData
     */
    public function fillInfo($reviewData)
    {
        if (is_string($reviewData)) {
            $reviewData = $this->loadData($reviewData);
        }
        $reviewData = $this->arrayEmptyClear($reviewData);

        if (isset($reviewData['product_to_review'])) {
            $this->openProduct($reviewData['product_to_review']);
        }
        $this->fillForm($reviewData);
        if (isset($reviewData['detailed_rating_select'])) {
            $this->fillRatings($reviewData['detailed_rating_select']);
        }
    }

    /**
     * Fills ratings
     *
     * @param array $detailedRatings
     */
    public function fillRatings(array $detailedRatings)
    {
        foreach ($detailedRatings as $value) {
            if (isset($value['rating_name']) &&  isset($value['stars'])) {
                $this->addParameter('ratingName' , $value['rating_name']);
                $this->addParameter('stars' , $value['stars']);
                $this->fillForm(array('detailed_rating' => 'yes'));
            } else {
                $this->fail('Incorrect data to fill');
            }
        }
    }

    /**
     * Open Review and delete
     *
     * @param array $searchData
     */
    public function deleteReview(array $searchData = array())
    {
        if ($searchData) {
            $this->openReview($searchData);
        }
        $this->clickButtonAndConfirm('delete_review', 'confirmation_for_delete');
    }

    /**
     * Verify Review
     *
     * @param array|string $reviewData
     */
    public function verifyReviewData($reviewData)
    {
        if (is_string($reviewData)) {
            $reviewData = $this->loadData($reviewData);
        }
        $reviewData = $this->arrayEmptyClear($reviewData);
        $simpleVerify = array();
        foreach ($reviewData as $fieldKey => $fieldValue) {
            if (!is_array($fieldValue)) {
                $simpleVerify[$fieldKey] = $fieldValue;
            }
        }
        $this->assertTrue($this->verifyForm($simpleVerify), $this->messages);
    }

    #********************************************
    #           Frontend Methods                *
    #********************************************

    /**
     * <p>Create Review</p>
     *
     * @param array|string $reviewData
     */
    public function frontendAddReview($reviewData)
    {
        if (is_string($reviewData)) {
            $reviewData = $this->loadData($reviewData);
        }
        $reviewData = $this->arrayEmptyClear($reviewData);
        $linkName = ($this->controlIsPresent('link', 'add_your_review')) ? 'add_your_review' : 'first_review';
        $this->defineCorrectParam($linkName, 'productId');
        $this->clickControl('link',$linkName);
        $this->fillForm($reviewData);
        if(isset($reviewData['ratings'])){
            $this->frontendAddRating($reviewData['ratings']);
        }
        $this->clickButton('submit_review');
    }

    /**
     * Define parameter %Id% from Control
     *
     * @return integer
     */
    protected function defineIdFromControl($url, $idName)
    {
        // ID definition
        $item_id = 0;
        $title_arr = explode('/', $url);
        $title_arr = array_reverse($title_arr);
        foreach ($title_arr as $key => $value) {
            if (preg_match("/$idName$/", $value) && isset($title_arr[$key - 1])) {
                $item_id = $title_arr[$key - 1];
                break;
            }
        }
        return $item_id;
    }

    /**
     * Verification review on frontend
     *
     * @param string $reviewText
     * @param string $productName
     * @param boolean $loggedIn
     */
    public function frontendReviewVerificationMyAccount($reviewText, $productName, $loggedIn = FALSE)
    {
        $this->addParameter('productName', $productName);
        if($loggedIn){
        //Verification in "My Recent Reviews" area
        $this->navigate('customer_account');
        $this->assertTrue($this->controlIsPresent('link', 'product_name'),
                          "Cannot find product with name: $productName");
        $this->defineCorrectParam('product_name', 'reviewId');
        $this->clickControl('link', 'product_name');
        $xPath = $this->_getControlXpath('pageelement', 'review_details');
        $text = trim($this->getText($xPath));
        $this->assertEquals('0', strcmp($text, trim($reviewText)), "Text on the page {$text} is not equal {$reviewText}");
        $this->clickControl('link', 'back_to_my_reviews');
        //Verification in "My Account -> My Product Reviews"
        $xPath = $this->_getControlXpath('pageelement', 'review_details');
        $text = trim($this->getText($xPath));
        $this->assertEquals('0', strcmp($text, trim($reviewText)), "Text on the page {$text} is not equal {$reviewText}");
        } else{
            $this->fail('Customer is not logged in');
        }
    }

    /**
     * Add parameter ReviewId
     *
     * @param string $linkName
     * @param string $paramName
     */
    public function defineCorrectParam($linkName, $paramName)
    {
        $linkXpath = $this->_getControlXpath('link', $linkName);
        $url = $this->getAttribute($linkXpath . "/@href");
        $id = $this->defineIdFromControl($url,'id');
        $this->addParameter($paramName, $id);
        $categoryId = $this->defineIdFromControl($url, 'category');
        $this->addParameter('categoryId', $categoryId);
    }

    /**
     * Review verification after approve
     *
     * @param array $verificationData
     * @param string $productName
     */
    public function frontendReviewVerificationInCategory($verificationData, $productName)
    {
        $this->addParameter('productName', $productName);
        $reviewText = (isset($verificationData['review'])) ? $verificationData['review'] : NULL;
        $reviewNickname = (isset($verificationData['nickname'])) ? $verificationData['nickname'] : NULL;
        $reviewSummary = (isset($verificationData['summary_of_your_review'])) ? $verificationData['summary_of_your_review'] : NULL;
        //Verification on product page
        if($this->controlIsPresent('link', 'reviews')){
            $this->defineCorrectParam('reviews', 'productId');
            $this->clickControl('link', 'reviews');
            $this->addParameter('reviewerName', $reviewNickname);
            $xPath = $this->_getControlXpath('link', 'review_summary');
            $text = trim($this->getText($xPath));
            $this->assertEquals('0', strcmp($text, trim($reviewSummary)), "Text on the page {$text} is not equal {$reviewSummary}");
            $this->defineCorrectParam('review_summary', 'reviewId');
            $this->clickControl('link', 'review_summary');
        //Verification on Review Details page
            $xPath = $this->_getControlXpath('pageelement', 'review_details');
            $text = trim($this->getText($xPath));
            $this->assertEquals('0', strcmp($text, trim($reviewText)), "Text on the page {$text} is not equal {$reviewText}");
        } else {
            $this->fail('Review is not approved');
        }
    }

    /**
     * Filling In Rating
     *
     *@param array|string $ratingData
     */
    public function frontendAddRating($ratingData)
    {
        if (is_string($ratingData)) {
            $ratingData = $this->loadData($ratingData);
        }
        foreach($ratingData as $value){
            $this->addParameter('rateName', $value['rating_name']);
            $this->addParameter('rateId', $value['stars']);
            if($this->controlIsPresent('radiobutton', 'select_rate')){
                $this->fillForm(array('select_rate' => 'Yes'));
            } else {
                return FALSE;
            }
        }
    }

}
