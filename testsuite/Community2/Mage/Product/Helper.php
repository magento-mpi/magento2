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
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    /**
     * Delete all Custom Options
     *
     * @return void
     */
    public function deleteCustomOptions()
    {
        $this->openTab('custom_options');
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'custom_option_set');
        $optionsQty = $this->getXpathCount($fieldSetXpath);
        While ($optionsQty > 0) {
            $id = $this->getAttribute($fieldSetXpath . "[{$optionsQty}]/@id");
            $id = explode('_', $id);
            $optionId = end($id);
            $this->addParameter('optionId', $optionId);
            $this->clickButton('delete_option', false);
            $optionsQty--;
        }
    }
    /**
     * Get Custom Option Id By Title
     *
     * @param string
     * @return integer
     */
    public function getCustomOptionId($optionTitle)
    {
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'custom_option_set');
        $optionId = '';
        if ($this->isElementPresent($fieldSetXpath . "//input[@value='{$optionTitle}']")) {
            $id = $this->getAttribute($fieldSetXpath . "//input[@value='{$optionTitle}'][1]@id");
            $id = explode('_', $id);
            $optionId = end($id);
        }
        return $optionId;
    }
    /**
     * Check if product is present in products grid
     *
     * @param array $productData
     * @return bool
     */
    public function isProductPresentInGrid($productData)
    {
        $data = array('product_sku' => $productData['product_sku']);
        $this->_prepareDataForSearch($data);
        $xpathTR = $this->search($data, 'product_grid');
        if (!is_null($xpathTR)) {
            return true;
        } else {
            return false;
        }
    }
}