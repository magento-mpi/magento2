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
            $this->getCurrentLocationUimapPage()->assignParams($this->_paramsHelper);
            $isDiscloseCategory = $this->_getControlXpath('link', 'expand_category');
            $catXpath = $this->_getControlXpath('link', 'sub_category');

            if ($this->isElementPresent($isDiscloseCategory)) {
                $this->click($isDiscloseCategory);
                $this->pleaseWait();
            }
        }
        $this->waitForAjax();
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
            foreach ($correctRoot as $v) {
                $correctSubCat = array_merge($correctSubCat, $this->defineCorrectCategory($value, $v));
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
        $categoryData = $this->arrayEmptyClear($categoryData);
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
                        $this->productHelper()->assignProduct($value, $tab);
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

    /**
     * check that Categories Page is opened
     */
    public function checkCategoriesPage()
    {
        $currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        if ($currentPage != 'edit_manage_categories' && $currentPage != 'manage_categories') {
            $this->fail("Opened the wrong page: '" . $currentPage . "' (should be: 'manage_categories')");
        }
    }

    /**
     * Click button and confirm
     *
     * @param string $buttonName
     * @param string $message
     */
    public function deleteCategory($buttonName, $message)
    {
        $buttonXpath = $this->_getControlXpath('button', $buttonName);
        if ($this->isElementPresent($buttonXpath)) {
            $confirmation = $this->getCurrentLocationUimapPage()->findMessage($message);
            $this->chooseCancelOnNextConfirmation();
            $this->click($buttonXpath);
            if ($this->isConfirmationPresent()) {
                $text = $this->getConfirmation();
                if ($text == $confirmation) {
                    $this->chooseOkOnNextConfirmation();
                    $this->click($buttonXpath);
                    $this->getConfirmation();
                    $this->pleaseWait();

                    return true;
                } else {
                    $this->messages['error'][] = "The confirmation text incorrect: {$text}\n";
                }
            } else {
                $this->messages['error'][] = "The confirmation does not appear\n";
                $this->pleaseWait();
                $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());

                return true;
            }
        } else {
            $this->messages['error'][] = "There is no way to remove an item(There is no 'Delete' button)\n";
        }

        return false;
    }

    /**
     * Validates product information in catalog
     *
     * @param array|string $productsInfo
     * @return bool
     */
    public function frontValidateProduct($productsInfo)
    {
        if (is_string($productsInfo)) {
            $productsInfo = $this->loadData($productsInfo);
        }
        $productsInfo = $this->arrayEmptyClear($productsInfo);
        $category = (isset($productsInfo['category'])) ? $productsInfo['category'] : NULL;
        $productName = (isset($productsInfo['product_name'])) ? $productsInfo['product_name'] : NULL;
        $verificationData = (isset($productsInfo['verification'])) ? $productsInfo['verification'] : NULL;
        if ($category != NULL && $productName != NULL) {
            $foundIt = $this->frontSearchPageWithProduct($productName, $category);
            if (!$foundIt) {
                $this->fail('Could not find the product');
            }
        }
        $this->frontVerifyPrices($verificationData, $foundIt);
    }

    /**
     * Searches the page with the product in the catalog
     *
     * @param string $productName
     * @param string $category
     * @return mixed
     */
    public function frontSearchPageWithProduct($productName, $category)
    {
        $this->addParameter('category', $category);
        $this->getUimapPage('frontend', 'catalog_page')->assignParams($this->_paramsHelper);
        $this->frontend('catalog_page');
        $xpathNext = $this->_getControlXpath('link', 'next_page');
        $this->addParameter('productName', $productName);
        $xpathProduct = $this->_getControlXpath('link', 'product_name');
        $i = 1;
        for (;;) {
            if ($this->isElementPresent($xpathProduct)) {
                return $i;
            } else {
                if ($this->isElementPresent($xpathNext)) {
                    $i++;
                    $this->addParameter('param', '?p=' . $i);
                    $this->getUimapPage('frontend', 'catalog_page_index')->assignParams($this->_paramsHelper);
                    $this->navigate('catalog_page_index');
                } else {
                    return FALSE;
                }
            }
        }
    }

    /**
     * Verifies the correctness of prices in the catalog
     *
     * @param array $verificationData
     * @param int $pageNum
     */
    public function frontVerifyPrices(array $verificationData, $pageNum)
    {
        $this->addParameter('param', '?p=' . $pageNum);
        $this->getUimapPage('frontend', 'catalog_page_index')->assignParams($this->_paramsHelper);
        $page = $this->getCurrentLocationUimapPage();
        $xpathProduct = $this->_getControlXpath('link', 'product_name');
        $this->addParameter('productNameXpath', $xpathProduct);
        $pageelements = get_object_vars($page->getAllPageelements());
        foreach ($verificationData as $key => $value) {
            $this->addParameter('price', $value);
            $xpathPrice = $this->getCurrentLocationUimapPage()->getMainForm()->findPageelement($key);
            if (!$this->isElementPresent($xpathPrice)) {
                $this->messages['error'][] = 'Could not find element ' . $key . ' with price ' . $value;
            }
            unset($pageelements['ex_' . $key]);
        }
        foreach ($pageelements as $key => $value) {
            if (!preg_match('/^ex_/', $key)) {
                $value = preg_replace('/\%productNameXpath\%/', $xpathProduct, $value);
                if ($this->isElementPresent($value)) {
                    $this->messages['error'][] = 'Element ' . $key . ' is on the page';
                }
            }
        }
        if (!empty($this->messages['error'])) {
            $this->fail(implode("\n", $this->messages['error']));
        }
    }
}
