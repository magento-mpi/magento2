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
        $reviewSearch = $this->arrayEmptyClear($productSearch);
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

        $productToChoose = (isset($reviewData['product_to_review'])) ? $reviewData['product_to_review'] : NULL;
        if ($productToChoose) {
            $this->openProduct($productToChoose);
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

}
