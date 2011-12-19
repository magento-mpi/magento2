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
        $this->searchAndOpen($reviewSearch);
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
            if (isset($value['rating_name']) && isset($value['stars'])) {
                $this->addParameter('ratingName', $value['rating_name']);
                $this->addParameter('stars', $value['stars']);
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
    public function deleteReview(array $searchData)
    {
        $this->openReview($searchData);
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
        $this->assertTrue($this->verifyForm($simpleVerify), $this->getParsedMessages());
    }

    #********************************************
    #           Frontend Methods                *
    #********************************************

    /**
     * <p>Create Review</p>
     *
     * @param array|string $reviewData
     * @param bool $validateRating      In case $validateRating == TRUE - rating filling will be mandatory
     */
    public function frontendAddReview($reviewData, $validateRating = true)
    {
        if (is_string($reviewData)) {
            $reviewData = $this->loadData($reviewData);
        }
        $reviewData = $this->arrayEmptyClear($reviewData);
        $linkName = ($this->controlIsPresent('link', 'add_your_review')) ? 'add_your_review' : 'first_review';
        $this->defineCorrectParam($linkName);
        $this->clickControl('link', $linkName);
        $this->fillForm($reviewData);
        if (isset($reviewData['ratings'])) {
            $this->frontendAddRating($reviewData['ratings'], $validateRating);
        }
        $this->saveForm('submit_review');
    }

    /**
     * Filling In Rating
     *
     * @param array|string $ratingData
     */
    public function frontendAddRating($ratingData, $validateRating = true)
    {
        if (is_string($ratingData)) {
            $ratingData = $this->loadData($ratingData);
        }
        foreach ($ratingData as $value) {
            $this->addParameter('rateName', $value['rating_name']);
            $this->addParameter('rateId', $value['stars']);
            if ($this->controlIsPresent('radiobutton', 'select_rate')) {
                $this->fillForm(array('select_rate' => 'Yes'));
            } else {
                $xpath = $this->_getControlXpath('radiobutton', 'select_rate');
                $this->addVerificationMessage('Rating with name ' . $value['rating_name'] . ' is not on the page');
            }
        }
        if ($validateRating) {
            $this->assertEmptyVerificationErrors();
        }
    }

    /**
     * Review verification after approve
     * (@TODO doesn't work for several reviews posted by one nickname)
     *
     * @param array $verifyData
     * @param string $productName
     */
    public function frontVerifyReviewDisplaying(array $verifyData, $productName)
    {
        $this->addParameter('productName', $productName);

        $review = (isset($verifyData['review'])) ? $verifyData['review'] : '';
        $nickname = (isset($verifyData['nickname'])) ? $verifyData['nickname'] : '';
        $summary = (isset($verifyData['summary_of_your_review'])) ? $verifyData['summary_of_your_review'] : '';
        if ($this->controlIsPresent('link', 'reviews')) {
            //Verification on product page
            $this->defineCorrectParam('reviews');
            $this->clickControl('link', 'reviews');
            $this->addParameter('reviewerName', $nickname);
            if (!$this->controlIsPresent('pageelement', 'review_reviwer_name')) {
                $this->fail('Customer with nickname \'' . $nickname . '\' does not added approved review');
            }
            $actualSummary = $this->getText($this->_getControlXpath('link', 'review_summary'));
            $xpathReview = $this->_getControlXpath('pageelement', 'review_details') . '/text()[1]';
            $actualReview = trim($this->getText($xpathReview));
            $this->assertEquals($summary, $actualSummary,
                    'Review Summary is not equal to specified: (' . $summary . '!=' . $actualSummary . ')');
            $this->assertEquals($review, $actualReview,
                    'Review Text is not equal to specified: (' . $review . '!=' . $actualReview . ')');
            //Verification on Review Details page
            $this->clickControl('link', 'review_summary');
            $this->verifyTextPresent($productName,
                    $productName . ' product not display on Review Details page');
            $this->verifyTextPresent($review,
                    '\'' . $review . '\' review text not display on Review Details page');
            $this->assertEmptyVerificationErrors();
        } else {
            $this->fail('Product does not have approved review(s)');
        }
    }

    /**
     * Verification review on frontend
     * (@TODO doesn't work for several reviews posted by one nickname)
     *
     * @param array $reviewText
     * @param string $productName
     */
    public function frontVerifyReviewDisplayingInMyAccount($reviewData, $productName)
    {
        //Verification in "My Account"
        $this->navigate('customer_account');
        $this->addParameter('productName', $productName);
        $this->assertTrue($this->controlIsPresent('link', 'product_name'),
                "Can not find product with name: $productName in My Recent Reviews block");
        $this->clickControl('link', 'product_name');
        $this->assertTextPresent($reviewData['review'],
                '\'' . $reviewData['review'] . '\' review text not display on Review Details page');
        //Verification in "My Account -> My Product Reviews"
        $this->navigate('my_product_reviews');
        $this->assertTrue($this->controlIsPresent('link', 'product_name'),
                "Can not find product with name: $productName in My Product Reviews block");
    }

    /**
     * Add parameter ReviewId
     *
     * @param string $linkName
     * @param string $paramName
     */
    public function defineCorrectParam($linkName)
    {
        $url = $this->getAttribute($this->_getControlXpath('link', $linkName) . "/@href");
        $this->addParameter('categoryId', $this->defineParameterFromUrl('category', $url));
    }

}
