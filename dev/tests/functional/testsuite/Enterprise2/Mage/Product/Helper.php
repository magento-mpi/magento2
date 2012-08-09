<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class 
 */
class Enterprise2_Mage_Product_Helper extends Community2_Mage_Product_Helper
{
    /**
     * Choose custom options and additional products
     *
     * @param array $dataForBuy
     */
    public function frontAddProductToCart($dataForBuy = null)
    {
        $customize = $this->controlIsPresent('button', 'customize_and_add_to_cart');
        $customizeFieldset = $this->_getControlXpath('fieldset', 'customize_product_info');
        if ($customize) {
            $productInfoFieldset = $this->_getControlXpath('fieldset', 'product_info');
            $this->clickButton('customize_and_add_to_cart', false);
            $this->waitForElementVisible($customizeFieldset);
            $this->waitForElementPresent($productInfoFieldset . "/parent::*[@style='display: none;']");
        }
        parent::frontAddProductToCart($dataForBuy);
    }

    /**
     * Select Store View on product page
     *
     * @param $storeViewName
     * @throws PHPUnit_Framework_Exception
     */
    public function chooseStoreView($storeViewName)
    {

        $fieldXpath = $this->_getControlXpath('dropdown', 'choose_store_view');
        if (!$this->isElementPresent($fieldXpath) || !$this->isEditable($fieldXpath)) {
            throw new PHPUnit_Framework_Exception($fieldXpath . ' dropdown is either not present or disabled.');
        }
        if ($this->getSelectedValue($fieldXpath) == $storeViewName) {
            return;
        }
        $complexValue = explode('/', $storeViewName);
        $valueToSelect = array_pop($complexValue);
        $parentXpath = $fieldXpath; //Xpath of the needed option parent element
        for ($level = 0; $level < count($complexValue); $level++) {
            $nextNested = "/*[contains(@label,'" . $complexValue[$level] . "')]";
            $nextSibling = "/following-sibling::*[contains(@label,'" . $complexValue[$level] . "')][1]";
            if ($this->isElementPresent($parentXpath . $nextNested)) {
                $parentXpath .= $nextNested;
            } elseif ($this->isElementPresent($parentXpath . $nextSibling)) {
                $parentXpath .= $nextSibling;
            } else {
                throw new PHPUnit_Framework_Exception(
                    'Cannot find nested/sibling optgroup/option ' . $complexValue[$level]);
            }
        }
        if ($this->isElementPresent($parentXpath . "//option[contains(text(),'" . $valueToSelect . "')]")) {
            $optionValue = $this->getValue($parentXpath . "//option[contains(text(),'" . $valueToSelect . "')]");
            //Try to select by value first, since there may be options with equal labels.
            if (isset($optionValue)) {
                $this->select($fieldXpath, 'value=' . $optionValue);
            } else {
                $this->select($fieldXpath, 'label=' . $valueToSelect);
            }
        } else {
            $this->select($fieldXpath, 'regexp:' . preg_quote($valueToSelect));
        }

    }
    
    /**
     * Import custom options from existent product
     *
     * @param mixed $productSku String or Array of SKUs
     */
    public function importCustomOptions($productSku)
    {
        parent::importCustomOptions($productSku);
    }

    /**
     * Delete all custom options
     *
     * @return bool
     */
    public function deleteAllCustomOptions()
    {
        parent::deleteAllCustomOptions();
    }

    /**
     * Verify Custom Options
     *
     * @param array $customOptionData
     *
     * @return boolean
     */
    public function verifyCustomOption(array $customOptionData)
    {
        parent::verifyCustomOption($customOptionData);
    }
}
