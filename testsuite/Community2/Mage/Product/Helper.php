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
 *
 */
class Community2_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    /**
     * Change attribute set
     *
     * @param array $newAttributeSet
     */
    public function changeAttributeSet($newAttributeSet)
    {
        $dropdownXpath = $this->_getControlXpath('dropdown', 'choose_attribute_set');
        $currentAttributeSet = $this->getSelectedValue($dropdownXpath);
        $fieldXpath = $this->_getControlXpath('pageelement', 'product_page_title');
        $popupXpath = $this->_getControlXpath('fieldset', 'change_attribute_set');
        $actualTitle = $this->getText($fieldXpath);
        $newTitle = str_replace($newAttributeSet, $currentAttributeSet, $actualTitle);
        $this->clickButton('change_attribute_set', false)
            ->waitForElement($popupXpath);
        $this->fillDropdown('choose_attribute_set', $newAttributeSet);
        $this->addParameter('setId', $dropdownXpath)->clickButton('apply')
            ->validatePage();
        $this->assertNotSame($this->getText($fieldXpath), $newTitle,
            "Attribute set in title should be $newAttributeSet, but now it's $currentAttributeSet");
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
        $this->waitForElement($this->_getControlXpath('fieldset', 'select_product_custom_option'));
        foreach ($productData as $value) {
            $this->searchAndChoose($value);
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
        while ($this->isElementPresent($this->_getControlXpath('fieldset', 'custom_option_set'))) {
            if (!$this->controlIsPresent('button', 'delete_custom_option')) {
                $this->fail('Current location url: ' . $this->getLocation() . "\n"
                    . 'Current page: ' . $this->getCurrentPage() . "\nProblem with 'Delete Option' button.\n"
                    . 'Control is not present on the page');
            }
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
        $optionsQty = $this->getXpathCount($this->_getControlXpath('fieldset', 'custom_option_set'));
        $needCount = count($customOptionData);
        if ($needCount != $optionsQty) {
            $this->addVerificationMessage('Product must be contains ' . $needCount
                . ' Custom Option(s), but contains ' . $optionsQty);
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
        if ($this->isElementPresent($fieldsetXpath)) {
            $optionId = $this->getAttribute($fieldsetXpath . '@id');
            foreach (explode('_', $optionId) as $value) {
                if (is_numeric($value)) {
                    return $value;
                }
            }
        }
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
        $this->clickControl('dropdown', 'add_product_by_type', false);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->addParameter('setId', $this->defineParameterFromUrl('set'));
        $this->validatePage();
        if ($productType == 'configurable') {
            $this->fillConfigurableSettings($productData);
        }
    }

    /**
     * Fill Product info
     *
     * @param array $productData
     * @param string $productType
     */
    public function fillProductInfo(array $productData, $productType = 'simple')
    {
        $this->fillProductTab($productData);
        $this->categoryHelper()->productSelectCategory($productData);
        $this->fillProductTab($productData, 'prices');
        $this->fillProductTab($productData, 'meta_information');
        if ($productType == 'simple' || $productType == 'virtual') {
            $this->fillProductTab($productData, 'recurring_profile');
        }
        $this->fillProductTab($productData, 'design');
        $this->fillProductTab($productData, 'gift_options');
        $this->fillProductTab($productData, 'inventory');
        $this->fillProductTab($productData, 'websites');
        $this->fillProductTab($productData, 'related');
        $this->fillProductTab($productData, 'up_sells');
        $this->fillProductTab($productData, 'cross_sells');
        $this->fillProductTab($productData, 'custom_options');
        if ($productType == 'grouped' || $productType == 'configurable') {
            $this->fillProductTab($productData, 'associated');
        }
        if ($productType == 'bundle') {
            $this->fillProductTab($productData, 'bundle_items');
        }
        if ($productType == 'downloadable') {
            $this->fillProductTab($productData, 'downloadable_information');
        }
    }

    /**
     * Fill Product Tab
     *
     * @param array $productData
     * @param string $tabName Value - general|prices|meta_information|images|recurring_profile
     * |design|gift_options|inventory|websites|categories|related|up_sells
     * |cross_sells|custom_options|bundle_items|associated|downloadable_information
     *
     * @return bool
     */
    public function fillProductTab(array $productData, $tabName = 'general')
    {
        $tabData = array();
        $needFilling = false;

        foreach ($productData as $key => $value) {
            if (preg_match('/^' . $tabName . '/', $key)) {
                $tabData[$key] = $value;
            }
        }

        if ($tabData) {
            $needFilling = true;
        }

        $tabXpath = $this->_getControlXpath('tab', $tabName);
        if ($tabName == 'websites' && !$this->isElementPresent($tabXpath)) {
            $needFilling = false;
        }

        if (!$needFilling) {
            return true;
        }

        $this->openTab($tabName);

        switch ($tabName) {
            case 'prices':
                $arrayKey = 'prices_tier_price_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $value) {
                        $this->addTierPrice($value);
                    }
                }
                $this->fillForm($tabData, 'prices');
                $this->fillUserAttributesOnTab($tabData, $tabName);
                break;
            case 'websites':
                $websites = explode(',', $tabData[$tabName]);
                $websites = array_map('trim', $websites);
                foreach ($websites as $value) {
                    $this->selectWebsite($value);
                }
                break;
            case 'related':
            case 'up_sells':
            case 'cross_sells':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $value) {
                        $this->assignProduct($value, $tabName);
                    }
                }
                break;
            case 'custom_options':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $value) {
                        $this->addCustomOption($value);
                    }
                }
                break;
            case 'bundle_items':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    if (array_key_exists('ship_bundle_items', $tabData[$arrayKey])) {
                        $array['ship_bundle_items'] = $tabData[$arrayKey]['ship_bundle_items'];
                        $this->fillForm($array, 'bundle_items');
                    }
                    foreach ($tabData[$arrayKey] as $value) {
                        if (is_array($value)) {
                            $this->addBundleOption($value);
                        }
                    }
                }
                break;
            case 'associated':
                $arrayKey = $tabName . '_grouped_data';
                $arrayKey1 = $tabName . '_configurable_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $value) {
                        $this->assignProduct($value, $tabName);
                    }
                } elseif (array_key_exists($arrayKey1, $tabData) && is_array($tabData[$arrayKey1])) {
                    $attributeTitle = (isset($productData['configurable_attribute_title']))
                        ? $productData['configurable_attribute_title']
                        : null;
                    if (!$attributeTitle) {
                        $this->fail('Attribute Title for configurable product is not set');
                    }
                    $this->addParameter('attributeTitle', $attributeTitle);
                    $this->fillForm($tabData[$arrayKey1], $tabName);
                    foreach ($tabData[$arrayKey1] as $value) {
                        if (is_array($value)) {
                            $this->assignProduct($value, $tabName, $attributeTitle);
                        }
                    }
                }
                break;
            case 'downloadable_information':
                $arrayKey = $tabName . '_data';
                if (array_key_exists($arrayKey, $tabData) && is_array($tabData[$arrayKey])) {
                    foreach ($tabData[$arrayKey] as $key => $value) {
                        if (preg_match('/^downloadable_sample_/', $key) && is_array($value)) {
                            $this->addDownloadableOption($value, 'sample');
                        }
                        if (preg_match('/^downloadable_link_/', $key) && is_array($value)) {
                            $this->addDownloadableOption($value, 'link');
                        }
                    }
                }
                $this->fillForm($tabData[$arrayKey], $tabName);
                break;
            default:
                $this->fillForm($tabData, $tabName);
                $this->fillUserAttributesOnTab($tabData, $tabName);
                break;
        }
        return true;
    }

    /**
     * Verify product info
     *
     * @param array $productData
     * @param array $skipElements
     */
    public function verifyProductInfo(array $productData, $skipElements = array())
    {
        $nestedArrays = array();
        foreach ($productData as $key => $value) {
            if (is_array($value)) {
                $nestedArrays[$key] = $value;
                unset($productData[$key]);
            }
            if ($key == 'websites' or $key == 'categories') {
                $nestedArrays[$key] = $value;
                unset($productData[$key]);
            }
        }
        $this->verifyForm($productData, null, $skipElements);
        //Verify selected categories
        if (array_key_exists('categories', $nestedArrays)) {
            $this->isSelectedCategory($nestedArrays['categories']);
        }
        // Verify tier prices
        if (array_key_exists('prices_tier_price_data', $nestedArrays)) {
            $this->verifyTierPrices($nestedArrays['prices_tier_price_data']);
        }
        //Verify selected websites
        if (array_key_exists('websites', $nestedArrays)) {
            $tabXpath = $this->_getControlXpath('tab', 'websites');
            if ($this->isElementPresent($tabXpath)) {
                $this->openTab('websites');
                $websites = explode(',', $nestedArrays['websites']);
                $websites = array_map('trim', $websites);
                foreach ($websites as $value) {
                    $this->selectWebsite($value, 'verify');
                }
            }
        }
        //Verify assigned products for 'Related Products', 'Up-sells', 'Cross-sells' tabs
        if (array_key_exists('related_data', $nestedArrays)) {
            $this->openTab('related');
            foreach ($nestedArrays['related_data'] as $value) {
                $this->isAssignedProduct($value, 'related');
            }
        }
        if (array_key_exists('up_sells_data', $nestedArrays)) {
            $this->openTab('up_sells');
            foreach ($nestedArrays['up_sells_data'] as $value) {
                $this->isAssignedProduct($value, 'up_sells');
            }
        }
        if (array_key_exists('cross_sells_data', $nestedArrays)) {
            $this->openTab('cross_sells');
            foreach ($nestedArrays['cross_sells_data'] as $value) {
                $this->isAssignedProduct($value, 'cross_sells');
            }
        }
        // Verify Associated Products tab
        if (array_key_exists('associated_grouped_data', $nestedArrays)) {
            $this->openTab('associated');
            foreach ($nestedArrays['associated_grouped_data'] as $value) {
                $this->isAssignedProduct($value, 'associated');
            }
        }
        if (array_key_exists('associated_configurable_data', $nestedArrays)) {
            $this->openTab('associated');
            $attributeTitle = (isset($productData['configurable_attribute_title']))
                ? $productData['configurable_attribute_title']
                : null;
            if (!$attributeTitle) {
                $this->fail('Attribute Title for configurable product is not set');
            }
            $this->addParameter('attributeTitle', $attributeTitle);
            $this->verifyForm($nestedArrays['associated_configurable_data'], 'associated');
            foreach ($nestedArrays['associated_configurable_data'] as $value) {
                if (is_array($value)) {
                    $this->isAssignedProduct($value, 'associated', $attributeTitle);
                }
            }
        }
        if (array_key_exists('custom_options_data', $nestedArrays)) {
            $this->verifyCustomOption($nestedArrays['custom_options_data']);
        }
        if (array_key_exists('bundle_items_data', $nestedArrays)) {
            $this->verifyBundleOptions($nestedArrays['bundle_items_data']);
        }
        if (array_key_exists('downloadable_information_data', $nestedArrays)) {
            $samples = array();
            $links = array();
            foreach ($nestedArrays['downloadable_information_data'] as $key => $value) {
                if (preg_match('/^downloadable_sample_/', $key) && is_array($value)) {
                    $samples[$key] = $value;
                }
                if (preg_match('/^downloadable_link_/', $key) && is_array($value)) {
                    $links[$key] = $value;
                }
            }
            if ($samples) {
                $this->verifyDownloadableOptions($samples, 'sample');
            }
            if ($links) {
                $this->verifyDownloadableOptions($links, 'link');
            }
            $this->verifyForm($nestedArrays['downloadable_information_data'], 'downloadable_information');
        }
        // Error Output
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Verify that category is selected
     *
     * @param string $assignedCategories
     */
    public function isSelectedCategory($assignedCategories)
    {
        $categories = explode(',', $assignedCategories);
        $categories = array_map('trim', $categories);
        $this->openTab('general');
        foreach ($categories as $value) {
            $categoryName = end(explode('/', $value));
            $this->addParameter('categoryName', $categoryName);
            if (!$this->controlIsVisible('pageelement', 'category_name')) {
                $this->fail('Category with name ' . $this->_getControlXpath('pageelement', 'category_name') . ' was not found');
            }
        }
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
     * @param string $keyUp
     * @param array $productFields
     * @param string $productType
     */
    public function createProductWithAutogeneration(array $productData, $isSave = false, $keyUp = 'general_name',
        array $productFields = null, $productType = 'simple'
    ) {
        if (!empty($productFields)) {
            foreach ($productFields as $value) {
                unset($productData[$value]);
            }
        }
        $this->productHelper()->createProduct($productData, $productType, false);
        $this->openTab('general');
        $this->keyUp($this->_getControlXpath('field', $keyUp), ' ');
        if ($isSave) {
            $this->saveForm('save');
        }
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
        foreach ($placeholders as $value) {
            $productField = 'general_' . str_replace(array('{{', '}}'), '', $value);
            $maskData = $this->getValue($this->_getControlXpath('field', $productField));
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
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'custom_option_set');
        $optionsQty = $this->getXpathCount($fieldSetXpath);
        $optionId ='';
        While ($optionsQty > 0) {
            $elementId = $this->getAttribute($fieldSetXpath . "[{$optionsQty}]/@id");
            $elementId = explode('_', $elementId);
            foreach ($elementId as $id) {
                if (is_numeric($id)) {
                    $optionId = $id;
                }
            }
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
        $optionId = '';
        $fieldSetXpath = $this->_getControlXpath('fieldset', 'custom_option_set');
        if ($this->isElementPresent($fieldSetXpath . "//input[@value='{$optionTitle}']")) {
            $elementId = $this->getAttribute($fieldSetXpath . "//input[@value='{$optionTitle}'][1]@id");
            $elementId = explode('_', $elementId);
            foreach ($elementId as $id) {
                if (is_numeric($id)) {
                    $optionId = $id;
                }
            }
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

    /**
     * Fill in Product Settings tab
     *
     * @param array $dataForAttributesTab
     * @param array $dataForInventoryTab
     * @param array $dataForWebsitesTab
     */
    public function updateThroughMassAction($dataForAttributesTab, $dataForInventoryTab, $dataForWebsitesTab)
    {
        if(isset($dataForAttributesTab)) {
            $this->fillFieldset($dataForAttributesTab, 'attributes');
        }
        else {
            $this->fail('data for attributes tab is absent');
        }
        if(isset($dataForInventoryTab)) {
            $this->fillFieldset($dataForInventoryTab, 'inventory');
        }
        else {
            $this->fail('data for inventory tab is absent');
        }
        if(isset($dataForWebsitesTab)) {
            $this->fillFieldset($dataForWebsitesTab, 'add_product');
        }
        else {
            $this->fail('data for websites tab is absent');
        }
    }
}
