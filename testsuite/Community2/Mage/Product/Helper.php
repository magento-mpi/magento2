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
     * Create Product without saving
     *
     * @param array $productData
     * @param string $productType
     */
    public function createProductWithoutSave(array $productData, $productType = 'simple')
    {
        $this->clickButton('add_new_product');
        $this->fillProductSettings($productData, $productType);
        if ($productType == 'configurable') {
            $this->fillConfigurableSettings($productData);
        }
        $this->fillProductInfo($productData, $productType);
    }

    /**
     * Import custom options from existent product
     *
     * @param mixed $productSku String or Array of SKUs
     */
    public function importCustomOptions($productSku)
    {
        $this->openTab('custom_options');
        $this->clickButton('import_options', false);
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'select_product_custom_option');
        if ($this->isElementPresent($fieldSetXpath)) {
            if (is_string($productSku)) {
                $this->searchAndChoose(array('product_sku' => $productSku));
            }
            if (is_array($productSku)) {
                foreach ($productSku as $value) {
                    $this->searchAndChoose(array('product_sku' => $value));
                }
            }
        } else {
            $this->fail("Dialog window 'Select Product' wasn't appeared.");
        }
        $this->clickButton('import', false);
    }

    /**
     * Delete all custom options
     *
     * @param $customOptionData
     *
     * @return bool
     */
    public function deleteAllCustomOptions($customOptionData)
    {
        $this->openTab('custom_options');
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'custom_option_set');
        $optionsQty = $this->getXpathCount($fieldSetXpath);
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
                $xpath = "//div[@id='option_" . $optionId . "']//button[span='Delete Option']";
                if (!$this->isElementPresent($xpath) || !$this->isVisible($xpath)) {
                    $this->fail("Current location url: '" . $this->getLocation() . "'\nCurrent page: '"
                                . $this->getCurrentPage() . "'\nProblem with 'Delete Option' button, xpath '$xpath':\n"
                                . 'Control is not present on the page');
                }
                $this->click($xpath);
                $numRow++;
            }
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
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'custom_option_set');
        $optionsQty = $this->getXpathCount($fieldSetXpath);
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
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'custom_option_set');
        $id = $this->getAttribute($fieldSetXpath . "[$rowNum]/@id");
        $id = explode('_', $id);
        foreach ($id as $value) {
            if (is_numeric($value)) {
                return $value;
            }
        }

    }
}
