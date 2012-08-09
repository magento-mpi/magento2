<?php

/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Category
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Category_Helper extends Core_Mage_Category_Helper
{

    /**
     * Select category by path
     *
     * @param string $separator
     * @param string $categoryPath
     * @param string $fieldsetName
     */
    public function selectCategory($categoryPath, $fieldsetName = 'select_category', $separator = '/')
    {
        $nodes = explode($separator, $categoryPath);
        $rootCat = array_shift($nodes);
        $categoryContainer = "//*[@id='category-edit-container']//h3";

        $correctRoot = $this->defineCorrectCategory($rootCat, null, $fieldsetName);

        foreach ($nodes as $value) {
            $correctSubCat = array();
            foreach ($correctRoot as $v) {
                $correctSubCat = array_merge($correctSubCat, $this->defineCorrectCategory($value, $v, $fieldsetName));
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
     * Find category with valid name
     *
     * @param string $catName
     * @param null|string $parentCategoryId
     * @param string $fieldsetName
     *
     * @return array
     */
    public function defineCorrectCategory($catName, $parentCategoryId = null, $fieldsetName = 'select_category')
    {
        $isCorrectName = array();
        $categoryText = '/div/a/span';

        if (!$parentCategoryId) {
            $this->addParameter('rootName', $catName);
            $catXpath =
                $this->_getControlXpath('link', 'root_category', $this->_findUimapElement('fieldset', $fieldsetName));
        } else {
            $this->addParameter('parentCategoryId', $parentCategoryId);
            $this->addParameter('subName', $catName);
            $isDiscloseCategory =
                $this->_getControlXpath('link', 'expand_category', $this->_findUimapElement('fieldset', $fieldsetName));
            $catXpath =
                $this->_getControlXpath('link', 'sub_category', $this->_findUimapElement('fieldset', $fieldsetName));
            if ($this->isElementPresent($isDiscloseCategory)) {
                $this->click($isDiscloseCategory);
                $this->pleaseWait();
            }
        }
        $this->waitForAjax();

        $text = $this->getText($catXpath . '[1]' . $categoryText);
        $text = preg_replace('/ \([0-9]+\)/', '', $text);
        if ($catName === $text) {
            $isCorrectName[] = $this->getAttribute($catXpath . '[1]' . '/div/a/@id');
        }

        return $isCorrectName;
    }
}