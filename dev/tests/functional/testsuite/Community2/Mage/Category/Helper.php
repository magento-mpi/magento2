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