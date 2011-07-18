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

        if ($parentCategoryId == null) {
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
                $isCorrectName[] = $this->getAttribute($catXpath . '[' . $i . ']' . $categoryText . '@id');
            }
        }

        return $isCorrectName;
    }

    /**
     * Select category by path
     *
     * @param string $categotyPath
     */
    public function selectCategory($categotyPath)
    {
        $nodes = explode('/', $categotyPath);
        $rootCat = array_shift($nodes);

        $correctRoot = $this->defineCorrectCategory($rootCat);

        foreach ($nodes as $value) {
            $correctSubCat = array();

            for ($i = 0; $i < count($correctRoot); $i++) {
                $correctSubCat = array_merge($correctSubCat,
                        $this->defineCorrectCategory($value, $correctRoot[$i]));
            }
            $correctRoot = $correctSubCat;
        }

        if (count($correctRoot) > 0) {
            $this->click('//*[@id=\'' . array_shift($correctRoot) . '\']');
            $this->pleaseWait();
            if (count($nodes) > 0) {
                $pageName = end($nodes);
            } else {
                $pageName = $rootCat;
            }
            $openedPageName = $this->getText("//*[@id='category-edit-container']//h3");
            $openedPageName = preg_replace('/ \(ID\: [0-9]+\)/', '', $openedPageName);
            if ($pageName != $openedPageName) {
                $this->fail("Opened category with name '$openedPageName' but must be '$pageName'");
            }
        } else {
            $this->fail("Category with path='$categotyPath' could not be selected");
        }
    }

    /**
     * Fill in Category information
     *
     * @param array $categotyData
     */
    public function fillCategoryInfo(array $categotyData)
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
                $this->fillForm($categotyData, $tab);
            } else {
                $arrayKey = $tab . '_data';
                if (array_key_exists($arrayKey, $categotyData) && is_array($categotyData[$arrayKey])) {
                    foreach ($categotyData[$arrayKey] as $key => $value) {
                        $this->productHelper()->assignProduct($categotyData[$arrayKey][$key], $tab);
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
     * @param string $categotyPath
     * @param array $categotyData
     */
    public function createSubCategory($categotyPath, array $categotyData)
    {
        $this->selectCategory($categotyPath);
        $this->clickButton('add_sub_category', false);
        $this->pleaseWait();
        $this->fillCategoryInfo($categotyData);
        $this->saveForm('save_category');
    }

}
