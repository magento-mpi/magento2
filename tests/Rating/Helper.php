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
class Rating_Helper extends Mage_Selenium_TestCase
{

    /**
     * Creates rating
     *
     * @param $ratingData
     */
    public function createRating($ratingData)
    {
        $this->clickButton('add_new_rating');
        $this->fillTabs($ratingData);
        $this->saveForm('save_rating');
    }

    /**
     * Edit existing rating
     *
     * @param $ratingData
     * @param $searchData
     */
    public function editRating($ratingData, $searchData)
    {
        $this->openRating($searchData);
        $this->fillTabs($ratingData);
        $this->saveForm('save_rating');
    }

    /**
     * Opens rating
     *
     * @param array $ratingSearch
     */
    public function openRating(array $ratingSearch)
    {
        $ratingSearch = $this->arrayEmptyClear($ratingSearch);
        $xpathTR = $this->search($ratingSearch, 'manage_ratings_grid');
        $this->assertNotEquals(null, $xpathTR, 'Rating is not found');
        $names = $this->shoppingCartHelper()->getColumnNamesAndNumbers('grid_head', false);
        if (array_key_exists('Rating Name', $names)) {
            $text = trim($this->getText($xpathTR . '//td[' . $names['Rating Name'] . ']'));
            $this->addParameter('elementTitle', $text);
        }
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage($this->_findCurrentPageFromUrl($this->getLocation()));
    }

    /**
     * Fills tabs in new/edit rating
     *
     * @param string|array $ratingData
     */
    public function fillTabs($ratingData)
    {
        if (is_string($ratingData)) {
            $ratingData = $this->loadData($ratingData);
        }
        $ratingData = $this->arrayEmptyClear($ratingData);
        foreach ($ratingData as $key => $value) {
            $this->fillForm($value, $key);
            if (isset($value['store_view_titles'])) {
                $this->fillRatingTitles($value['store_view_titles']);
            }
        }
    }

    /**
     * Fills rating titles for each store view
     *
     * @param array $storeViewTitles
     */
    public function fillRatingTitles(array $storeViewTitles)
    {
        foreach ($storeViewTitles as $value) {
            if (isset($value['store_view_name'])) {
                $this->addParameter('storeViewName', $value['store_view_name']);
            }
            if (isset($value['store_view_title'])) {
                $this->fillForm(array('store_view_title' => $value['store_view_title']));
            }
        }
    }

    /**
     * Open Rating and delete
     *
     * @param array $searchData
     */
    public function deleteRating(array $searchData = array())
    {
        if ($searchData) {
            $this->openRating($searchData);
        }
        $this->clickButtonAndConfirm('delete_rating', 'confirmation_for_delete');
    }

    /**
     * Verify Rating
     *
     * @param array|string $ratingData
     */
    public function verifyRatingData($ratingData)
    {
        if (is_string($ratingData)) {
            $ratingData = $this->loadData($ratingData);
        }
        $ratingData = $this->arrayEmptyClear($ratingData);
        $simpleVerify = array();
        $specialVerify = array();
        foreach ($ratingData as $tabData) {
            if (is_array($tabData)) {
                foreach ($tabData as $fieldKey => $fieldValue) {
                    if (is_array($fieldValue)) {
                        $specialVerify[$fieldKey] = $fieldValue;
                    } else {
                        $simpleVerify[$fieldKey] = $fieldValue;
                    }
                }
            }
        }
        $this->assertTrue($this->verifyForm($simpleVerify), $this->messages);
    }

}
