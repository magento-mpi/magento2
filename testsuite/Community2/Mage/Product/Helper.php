<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Helper class
 */
class Community2_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    /**
     * Change attribute set
     *
     * @param string $newAttributeSet
     */
    public function changeAttributeSet($newAttributeSet)
    {
        $actualTitle = $this->getControlAttribute('pageelement', 'product_page_title', 'text');
        $this->clickButton('change_attribute_set', false);
        $this->waitForElementEditable($this->_getControlXpath('dropdown', 'choose_attribute_set'));
        $currentSetName = $this->getControlAttribute('dropdown', 'choose_attribute_set', 'selectedLabel');
        $this->fillDropdown('choose_attribute_set', $newAttributeSet);
        $newTitle = str_replace($currentSetName, $newAttributeSet, $actualTitle);
        $param = $this->getControlAttribute('dropdown', 'choose_attribute_set', 'selectedValue');
        $this->addParameter('setId', $param);
        $this->clickButton('apply');
        $this->assertSame($newTitle, $this->getControlAttribute('pageelement', 'product_page_title', 'text'),
            "Attribute set in title should be $newAttributeSet, but now it's $currentSetName");
    }

    /**
     * Import custom options from existent product
     *
     * @param array $productData
     */
    public function importCustomOptions(array $productData)
    {
        $this->openTab('custom_options');
        $this->clickButton('import_options', false);
        $this->waitForElementVisible($this->_getControlXpath('fieldset', 'select_product_custom_option'));
        foreach ($productData as $value) {
            $this->searchAndChoose($value, 'select_product_custom_option_grid');
        }
        $this->clickButton('import', false);
        $this->pleaseWait();
    }

    /**
     * Delete all custom options
     */
    public function deleteAllCustomOptions()
    {
        $this->openTab('custom_options');
        while ($this->controlIsPresent('fieldset', 'custom_option_set')) {
            $this->assertTrue($this->buttonIsPresent('button', 'delete_custom_option'),
                $this->locationToString() . "Problem with 'Delete Option' button.\n"
                . 'Control is not present on the page');
            $this->clickButton('delete_custom_option', false);
        }
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
        $optionsQty = $this->getControlCount('fieldset', 'custom_option_set');
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
     * @param int $rowNum
     *
     * @return int
     */
    public function getOptionId($rowNum)
    {
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'custom_option_set') . "[$rowNum]";
        $availableElement = $this->elementIsPresent($fieldsetXpath);
        if ($availableElement) {
            $optionId = $availableElement->attribute('id');
            foreach (explode('_', $optionId) as $value) {
                if (is_numeric($value)) {
                    return $value;
                }
            }
        }
        return '';
    }

    /**
     * Create Product method using "Add Product" split button
     *
     * @param array $productData
     * @param string $productType
     * @param bool $isSave
     */
    public function createProduct(array $productData, $productType = 'simple', $isSave = true)
    {
        $this->selectTypeProduct($productData, $productType);
        if ($productData['product_attribute_set'] != 'Default') {
            $this->changeAttributeSet($productData['product_attribute_set']);
        }
        if ($productType == 'configurable') {
            $this->fillConfigurableSettings($productData);
        }
        $this->fillProductInfo($productData, $productType);
        if ($isSave) {
            $this->saveForm('save');
        }
    }

    /**
     * Select product type
     *
     * @param array $productData
     * @param string $productType
     */
    public function selectTypeProduct(array $productData, $productType)
    {
        $this->clickButton('add_new_product_split_select', false);
        $this->addParameter('productType', $productType);
        $this->clickButton('add_product_by_type', false);
        $this->waitForPageToLoad();
        $this->addParameter('setId', $this->defineParameterFromUrl('set'));
        $this->validatePage();
    }

    /**
     * Get auto-incremented SKU
     *
     * @param string $productSku
     *
     * @return string
     */
    public function getGeneratedSku($productSku)
    {
        return $productSku . '-1';
    }

    /**
     * Creating product using fields autogeneration with variables on General tab
     *
     * @param array $productData
     * @param bool $isSave
     * @param array $skipFieldFillIn
     * @param string $productType
     */
    public function createProductWithAutoGeneration(array $productData, $isSave = false, $skipFieldFillIn = array(), $productType = 'simple')
    {
        if (!empty($skipFieldFillIn)) {
            foreach ($skipFieldFillIn as $value) {
                unset($productData[$value]);
            }
        }
        $this->createProduct($productData, $productType, $isSave);
        //$this->openTab('general');
        //$this->keyUp($this->_getControlXpath('field', $keyUp), ' ');
        //    if ($isSave) {
        //        $this->saveForm('save');
        //    }
    }

    /**
     * Form mask's value replacing variable in mask with variable field's value on General tab
     *
     * @param string $mask
     * @param array $placeholders
     *
     * @return string
     */
    public function formFieldValueFromMask($mask, array $placeholders)
    {
        $this->openTab('general');
        foreach ($placeholders as $value) {
            $productField = 'general_' . str_replace(array('{{', '}}'), '', $value);
            $maskData = $this->getControlAttribute('field', $productField, 'value');
            $mask = str_replace($value, $maskData, $mask);
        }
        return $mask;
    }

    /**
     * Delete all Custom Options
     *
     * @return void
     */
    public function deleteCustomOptions()
    {
        $this->openTab('custom_options');
        $customOptions = $this->getControlElements('fieldset', 'custom_option_set');
        /**
         * @var PHPUnit_Extensions_Selenium2TestCase_Element $customOption
         */
        foreach ($customOptions as $customOption) {
            $optionId = '';
            $elementId = explode('_', $customOption->attribute('id'));
            foreach ($elementId as $id) {
                if (is_numeric($id)) {
                    $optionId = $id;
                }
            }
            $this->addParameter('optionId', $optionId);
            $this->clickButton('delete_option', false);
        }
    }

    /**
     * Get Custom Option Id By Title
     *
     * @param string
     *
     * @return integer
     */
    public function getCustomOptionId($optionTitle)
    {
        $optionSetElement = $this->getControlElement('fieldset', 'custom_option_set');
        $optionElements = $this->getChildElements($optionSetElement, "//input[@value='{$optionTitle}']", false);
        if (!empty($optionElements)) {
            /**
             * @var PHPUnit_Extensions_Selenium2TestCase_Element $element
             */
            list($element) = $optionElements;
            $elementId = $element->attribute('id');
            foreach (explode('_', $elementId) as $id) {
                if (is_numeric($id)) {
                    return $id;
                }
            }
        }
        return null;
    }

    /**
     * Check if product is present in products grid
     *
     * @param array $productData
     *
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

    /**
     * Fill in Product Settings tab
     *
     * @param array $dataForAttributesTab
     * @param array $dataForInventoryTab
     * @param array $dataForWebsitesTab
     */
    public function updateThroughMassAction($dataForAttributesTab, $dataForInventoryTab, $dataForWebsitesTab)
    {
        if (isset($dataForAttributesTab)) {
            $this->fillFieldset($dataForAttributesTab, 'attributes');
        } else {
            $this->fail('data for attributes tab is absent');
        }
        if (isset($dataForInventoryTab)) {
            $this->fillFieldset($dataForInventoryTab, 'inventory');
        } else {
            $this->fail('data for inventory tab is absent');
        }
        if (isset($dataForWebsitesTab)) {
            $this->fillFieldset($dataForWebsitesTab, 'add_product');
        } else {
            $this->fail('data for websites tab is absent');
        }
    }
}
