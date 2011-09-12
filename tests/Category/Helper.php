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
class Category_Helper extends Mage_Selenium_TestCase
{

    /**
     * Find category with valid name
     *
     * @param string $catName
     * @param null|string $parentCategoryId
     * @return array
     */
    public function defineCorrectCategory($catName, $parentCategoryId = null)
    {
        $isCorrectName = array();
        $categoryText = '/div/a/span';

        if (!$parentCategoryId) {
            $this->addParameter('rootName', $catName);
            $catXpath = $this->_getControlXpath('link', 'root_category');
        } else {
            $this->addParameter('parentCategoryId', $parentCategoryId);
            $this->addParameter('subName', $catName);
            $isDiscloseCategory = $this->_getControlXpath('link', 'expand_category');
            $catXpath = $this->_getControlXpath('link', 'sub_category');

            if ($this->isElementPresent($isDiscloseCategory)) {
                $this->click($isDiscloseCategory);
                $this->pleaseWait();
            }
        }

        $qtyCat = $this->getXpathCount($catXpath . $categoryText);

        for ($i = 1; $i <= $qtyCat; $i++) {
            $text = $this->getText($catXpath . '[' . $i . ']' . $categoryText);
            $text = preg_replace('/ \([0-9]+\)/', '', $text);
            if ($catName === $text) {
                $isCorrectName[] = $this->getAttribute($catXpath . '[' . $i . ']' . '/div/a/@id');
            }
        }

        return $isCorrectName;
    }

    /**
     * Select category by path
     *
     * @param string $categoryPath
     */
    public function selectCategory($categoryPath)
    {
        $nodes = explode('/', $categoryPath);
        $rootCat = array_shift($nodes);
        $categoryContainer = "//*[@id='category-edit-container']//h3";

        $correctRoot = $this->defineCorrectCategory($rootCat);

        foreach ($nodes as $value) {
            $correctSubCat = array();

            for ($i = 0; $i < count($correctRoot); $i++) {
                $correctSubCat = array_merge($correctSubCat, $this->defineCorrectCategory($value, $correctRoot[$i]));
            }
            $correctRoot = $correctSubCat;
        }

        if ($correctRoot) {
            $this->click('//*[@id=\'' . array_shift($correctRoot) . '\']');
            if ($this->isElementPresent($categoryContainer)) {
                $this->pleaseWait();
            }
            if ($nodes) {
                $pageName = end($nodes);
            } else {
                $pageName = $rootCat;
            }
            if ($this->isElementPresent($categoryContainer)) {
                $openedPageName = $this->getText($categoryContainer);
                $openedPageName = preg_replace('/ \(ID\: [0-9]+\)/', '', $openedPageName);
                if ($pageName != $openedPageName) {
                    $this->fail("Opened category with name '$openedPageName' but must be '$pageName'");
                }
            }
        } else {
            $this->fail("Category with path='$categoryPath' could not be selected");
        }
    }

    /**
     * Fill in Category information
     *
     * @param array $categoryData
     */
    public function fillCategoryInfo(array $categoryData)
    {
        $page = $this->getCurrentLocationUimapPage();
        $tabs = $page->getAllTabs();
        foreach ($tabs as $tab => $values) {
            $tabXpath = $page->findTab($tab)->getXpath();
            $isTabOpened = $this->getAttribute($tabXpath . '/parent::*/@class');
            if (!preg_match('/active/', $isTabOpened)) {
                $this->clickControl('tab', $tab, FALSE);
            }
            if ($tab != 'category_products') {
                $this->fillForm($categoryData, $tab);
            } else {
                $arrayKey = $tab . '_data';
                if (array_key_exists($arrayKey, $categoryData) && is_array($categoryData[$arrayKey])) {
                    foreach ($categoryData[$arrayKey] as $key => $value) {
                        $this->productHelper()->assignProduct($categoryData[$arrayKey][$key], $tab);
                    }
                }
            }
        }
    }

    /**
     * Create Root category
     *
     * @param array $categotyData
     */
    public function createRootCategory(array $categotyData)
    {
        $this->clickButton('add_root_category', false);
        $this->pleaseWait();
        $this->fillCategoryInfo($categotyData);
        $this->saveForm('save_category');
    }

    /**
     * Create Sub category
     *
     * @param string $categoryPath
     * @param array $categoryData
     */
    public function createSubCategory($categoryPath, array $categoryData)
    {
        $this->selectCategory($categoryPath);
        $this->clickButton('add_sub_category', false);
        $this->pleaseWait();
        $this->fillCategoryInfo($categoryData);
        $this->saveForm('save_category');
    }

}
