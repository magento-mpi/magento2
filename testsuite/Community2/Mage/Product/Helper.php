<?php
# Magento
#
# {license_notice}
#
# @category    Magento
# @package     Mage_Product
# @subpackage  functional_tests
# @copyright   {copyright}
# @license     {license_link}
#
/**
 * Helper class
 */
class Community2_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    /**
     * Import custom options from existent product
     *
     * @param array $productData
     */
    public function importCustomOptions(array $productData)
    {
        $this->openTab('custom_options');
        $this->clickButton('import_options', false);
        $this->waitForElement($this->_getControlXpath('fieldset', 'select_product_custom_option'));
        foreach ($productData as $value) {
            $this->searchAndChoose($value);
        }
        $this->clickButton('import', false);
        $this->pleaseWait();
    }

    /**
     * Delete all custom options
     *
     * @return bool
     */
    public function deleteAllCustomOptions()
    {
        $this->openTab('custom_options');
        while ($this->isElementPresent($this->_getControlXpath('fieldset', 'custom_option_set') . '[1]')) {
            $xpath = $this->_getControlXpath('fieldset', 'custom_option_set') . "[1]//button[span='Delete Option']";
            if (!$this->isElementPresent($xpath) || !$this->isVisible($xpath)) {
                $this->fail(
                    "Current location url: '" . $this->getLocation() . "'\nCurrent page: '" . $this->getCurrentPage()
                    . "'\nProblem with 'Delete Option' button, xpath '$xpath':\n"
                    . 'Control is not present on the page');
            }
            $this->click($xpath);
        }
        return true;
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
        $this->openTab('custom_options');
        $optionsQty = $this->getXpathCount($this->_getControlXpath('fieldset', 'custom_option_set'));
        $needCount = count($customOptionData);
        if ($needCount != $optionsQty) {
            $this->addVerificationMessage(
                'Product must be contains ' . $needCount . ' Custom Option(s), but contains ' . $optionsQty);
            return false;
        }
        $numRow = 1;
        foreach ($customOptionData as $value) {
            if (is_array($value)) {
                $optionId = $this->getOptionId($numRow);
                $this->addParameter('optionId', $optionId);
                $this->verifyForm($value, 'custom_options');
                $numRow++;
            }
        }
        return true;
    }

    /**
     * Get option id for selected row
     *
     * @param mixed $rowNum
     *
     * @return mixed
     */
    public function getOptionId($rowNum)
    {
        $id = $this->getAttribute($this->_getControlXpath('fieldset', 'custom_option_set') . "[$rowNum]@id");
        $id = explode('_', $id);
        foreach ($id as $value) {
            if (is_numeric($value)) {
                return $value;
            }
        }
    }
}
