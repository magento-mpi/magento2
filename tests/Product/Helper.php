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
class Product_Helper extends Mage_Selenium_TestCase
{

    /**
     * Fill in Product Settings tab
     *
     * @param array $productData
     * @param string $productType Value - simple|virtual|bundle|configurable|downloadable|grouped
     */
    public function fillProductSettings($productData, $productType='simple')
    {
        $attributeSet = (isset($productData['product_attribute_set']))
                            ? $productData['product_attribute_set']
                            : null;

        $attributeSetXpath = $this->_getControlXpath('dropdown', 'product_attribute_set');
        $productTypeXpath = $this->_getControlXpath('dropdown', 'product_type');

        if (!empty($attributeSet)) {
            $this->select($attributeSetXpath, 'label=' . $attributeSet);
            $attributeSetID = $this->getValue($attributeSetXpath . '/option[text()=\'' . $attributeSet . '\']');
        } else {
            $attributeSetID = $this->getValue($attributeSetXpath . "/option[@selected='selected']");
        }
        $this->select($productTypeXpath, 'value=' . $productType);

        $productParameters = 'set/' . $attributeSetID . '/type/' . $productType . '/';
        $this->addParameter('productParameters', $productParameters);

        $this->clickButton('continue');
    }

    /**
     * Select Dropdown Attribute(s) for configurable product creation
     *
     * @param array $productData
     */
    public function fillConfigurableSettings(array $productData)
    {
        $productParameters = $this->_paramsHelper->getParameter('productParameters');

        $attributes = (isset($productData['configurable_attribute_title']))
                        ? explode(',', $productData['configurable_attribute_title'])
                        : null;

        if (!empty($attributes)) {
            $attributesId = array();
            $attributes = array_map('trim', $attributes);

            foreach ($attributes as $attributeTitle) {
                $this->addParameter('attributeTitle', $attributeTitle);
                $xpath = $this->_getControlXpath('checkboxe', 'configurable_attribute_title');
                if ($this->isElementPresent($xpath)) {
                    $attributesId[] = $this->getAttribute($xpath . '/@value');
                    $this->click($xpath);
                } else {
                    $this->fail("Dropdown attribute with title '$attributeTitle' is not present on the page");
                }
            }

            $attributesUrl = urlencode(base64_encode(implode(',', $attributesId)));
            $productParameters = 'attributes/' . $attributesUrl . '/' . $productParameters;
            $this->addParameter('productParameters', $productParameters);

            $this->clickButton('continue');
        } else {
            $this->fail('Dropdown attribute for configurable product creation is not set');
        }
    }

    /**
     * Fill Product Tab
     *
     * @param array $productData
     * @param string $tabName Value - general|prices|meta_information|images|recurring_profile
     * |design|gift_options|inventory|websites|categories|related_products|up_sells_products
     * |cross_sells_products|custom_options|bundle_items|associated_products|downloadable_information
     */
    public function fillTab(array $productData, $tabName = 'general')
    {
        $needFilling = FALSE;
        $waitAjax = False;
        foreach ($productData as $key => $value) {
            if (preg_match('/^' . $tabName . '/', $key)) {
                $needFilling = TRUE;
                break;
            }
        }

        $tabXpath = $this->getCurrentLocationUimapPage()->findTab($tabName)->getXpath();
        if ($tabName == 'websites' && !$this->isElementPresent($tabXpath)) {
            $needFilling = FALSE;
        }

        if ($needFilling) {
            $isTabOpened = $this->getAttribute($tabXpath . '/parent::*/@class');
            if (!preg_match('/active/', $isTabOpened)) {
                if (preg_match('/ajax/', $isTabOpened)) {
                    $waitAjax = TRUE;
                }
                $this->clickControl('tab', $tabName, FALSE);
                if ($waitAjax) {
                    $this->pleaseWait();
                }
            }
            switch ($tabName) {
                case 'prices':
                    $arrayKey = 'prices_tier_price_data';
                    if (array_key_exists($arrayKey, $productData) && is_array($productData[$arrayKey])) {
                        foreach ($productData[$arrayKey] as $key => $value) {
                            $this->addTierPrice($productData[$arrayKey][$key]);
                        }
                    }
                    $this->fillForm($productData, 'prices');
                    $this->fillUserAttributesOnTab($productData, $tabName);
                    break;
                case 'websites':
                    $valuesArray = explode(',', $productData['websites']);
                    $valuesArray = array_map('trim', $valuesArray);
                    foreach ($valuesArray as $value) {
                        $this->selectWebsite($value);
                    }
                    break;
                case 'categories':
                    $categories = explode(',', $productData['categories']);
                    $categories = array_map('trim', $categories);
                    foreach ($categories as $value) {
                        $this->categoryHelper()->selectCategory($value);
                    }
                    break;
                case 'related_products': case 'up_sells_products': case 'cross_sells_products':
                    $arrayKey = $tabName . '_data';
                    if (array_key_exists($arrayKey, $productData) && is_array($productData[$arrayKey])) {
                        foreach ($productData[$arrayKey] as $key => $value) {
                            $this->assignProduct($productData[$arrayKey][$key], $tabName);
                        }
                    }
                    break;
                case 'custom_options':
                    $arrayKey = $tabName . '_data';
                    if (array_key_exists($arrayKey, $productData) && is_array($productData[$arrayKey])) {
                        foreach ($productData[$arrayKey] as $key => $value) {
                            $this->addCustomOption($productData[$arrayKey][$key]);
                        }
                    }
                    break;
                case 'bundle_items':
                    $arrayKey = $tabName . '_data';
                    if (array_key_exists($arrayKey, $productData) && is_array($productData[$arrayKey])) {
                        if (array_key_exists('ship_bundle_items', $productData[$arrayKey])) {
                            $array['ship_bundle_items'] = $productData[$arrayKey]['ship_bundle_items'];
                            $this->fillForm($array, 'bundle_items');
                        }
                        foreach ($productData[$arrayKey] as $key => $value) {
                            if (is_array($productData[$arrayKey][$key])) {
                                $this->addBundleOption($productData[$arrayKey][$key]);
                            }
                        }
                    }
                    break;
                case 'associated_products':
                    $arrayKey = $tabName . '_grouped_data';
                    $arrayKey1 = $tabName . '_configurable_data';
                    if (array_key_exists($arrayKey, $productData) && is_array($productData[$arrayKey])) {
                        foreach ($productData[$arrayKey] as $key => $value) {
                            $this->assignProduct($productData[$arrayKey][$key], $tabName);
                        }
                    } elseif (array_key_exists($arrayKey1, $productData) && is_array($productData[$arrayKey1])) {
                        $attributeTitle = $productData['configurable_attribute_title'];
                        $this->addParameter('attributeTitle', $attributeTitle);
                        $this->fillForm($productData[$arrayKey1], $tabName);
                        foreach ($productData[$arrayKey1] as $key => $value) {
                            if (is_array($productData[$arrayKey1][$key])) {
                                $this->assignProduct($productData[$arrayKey1][$key], $tabName, $attributeTitle);
                            }
                        }
                    }
                    break;
                case 'downloadable_information':
                    $arrayKey = $tabName . '_data';
                    if (array_key_exists($arrayKey, $productData) && is_array($productData[$arrayKey])) {
                        foreach ($productData[$arrayKey] as $key => $value) {
                            if (preg_match('/^downloadable_sample_/', $key)
                                    && is_array($productData[$arrayKey][$key])) {
                                $this->addDownloadableOption($productData[$arrayKey][$key], 'samples');
                            }
                            if (preg_match('/^downloadable_link_/', $key) && is_array($productData[$arrayKey][$key])) {
                                $this->addDownloadableOption($productData[$arrayKey][$key], 'links');
                            }
                        }
                    }
                    $this->fillForm($productData[$arrayKey], $tabName);
                    break;
                default:
                    $this->fillForm($productData, $tabName);
                    $this->fillUserAttributesOnTab($productData, $tabName);
                    break;
            }
        }
    }

    /**
     * Add Tier Price
     *
     * @param array $tierPriceData
     */
    public function addTierPrice(array $tierPriceData)
    {
        $page = $this->getCurrentLocationUimapPage();
        $fieldSetXpath = $page->findFieldset('product_prices')->getXpath();
        $rowNumber = $this->getXpathCount($fieldSetXpath . "//*[@id='tier_price_container']/tr");
        $this->addParameter('tierPriceId', $rowNumber);
        $page->assignParams($this->_paramsHelper);
        $this->clickButton('add_tier_price', FALSE);
        $this->fillForm($tierPriceData, 'prices');
    }

    /**
     * Add Custom Option
     *
     * @param array $customOptionData
     */
    public function addCustomOption(array $customOptionData)
    {
        $page = $this->getCurrentLocationUimapPage();
        $fieldSetXpath = $page->findFieldset('product_custom_options')->getXpath();
        $optionId = $this->getXpathCount($fieldSetXpath . "//*[@class='option-box']") + 1;
        $this->addParameter('optionId', $optionId);
        $page->assignParams($this->_paramsHelper);
        $this->clickButton('add_option', FALSE);
        $this->fillForm($customOptionData, 'custom_options');
        foreach ($customOptionData as $row_key => $row_value) {
            if (preg_match('/^custom_option_row/', $row_key) and is_array($customOptionData[$row_key])) {
                $rowId = $this->getXpathCount($fieldSetXpath .
                        "//tr[contains(@id,'product_option_') and not(@style)]");
                $this->addParameter('rowId', $rowId);
                $page->assignParams($this->_paramsHelper);
                $this->clickButton('add_row', FALSE);
                $this->fillForm($customOptionData[$row_key], 'custom_options');
            }
        }
    }

    /**
     * Select Website by Website name
     *
     * @param type $websiteName
     */
    public function selectWebsite($websiteName)
    {
        $fieldsetXpath = $this->getCurrentLocationUimapPage()->findFieldset('product_websites')->getXpath();
        $websiteXpath = $fieldsetXpath . "//*[text()='" . $websiteName . "']";
        $qtySite = $this->getXpathCount($websiteXpath);
        if ($qtySite > 0) {
            $websiteId = $this->getAttribute($websiteXpath . '/@for');
            $this->addParameter('websiteId', $websiteId);
            $this->getCurrentLocationUimapPage()->assignParams($this->_paramsHelper);
            $fieldXpath = $this->_getControlXpath('checkboxe', 'websites');
            if ($this->getValue($fieldXpath) == 'off') {
                $this->click($fieldXpath);
            }
        } else {
            $this->fail('Website with name "' . $websiteName . '" does not exist');
        }
    }

    /**
     * Assign product. Use for fill in 'Related Products', 'Up-sells' or 'Cross-sells' tabs
     *
     * @param array $data
     * @param string $tabName
     */
    public function assignProduct(array $data, $tabName, $attributeTitle = null)
    {
        // Prepare data for find and fill in
        $this->_prepareDataForSearch($data);
        if (empty($data)) {
            return true;
        }
        $needFilling = FALSE;
        $fillingData = array();
        $arrayKey = 'associated_products_by_attribute_value';

        foreach ($data as $key => $value) {
            if ($key == $tabName . '_position' || $key == $tabName . '_default_qty'
                    || $key == $tabName . '_price' || $key == $tabName . '_price_type') {
                $fillingData[$key] = $value;
                unset($data[$key]);
                $needFilling = True;
            }
        }

        if ($attributeTitle != null) {
            $this->addParameter('attributeCode', strtolower($attributeTitle));
        }

        //Search prodcut
        $this->searchAndChoose($data, $tabName);
        // Fill in additional data
        if ($needFilling) {
            if ($attributeTitle == null) {
                // Forming xpath for string that contains the lookup data
                $xpathTR = '//tr';
                foreach ($data as $key => $value) {
                    if (!preg_match('/_from/', $key) and !preg_match('/_to/', $key)) {
                        $xpathTR .= "[contains(.,'$value')]";
                    }
                }
                $this->addParameter('productXpath', $xpathTR);
            } else {
                //Forming xpath
                if (array_key_exists($arrayKey, $data)) {
                    $attributeValue = $data[$arrayKey];
                } else {
                    $this->fail("Value for attribute '$attributeTitle' isn't set");
                }
                $xpathTR = "//li[contains(div/text(),'$attributeTitle')]//li[div/strong='$attributeValue']";

                $this->addParameter('attributeSettings', $xpathTR);
            }
            $this->fillForm($fillingData, $tabName);
        }
    }

    /**
     * Add Bundle Option
     *
     * @param array $bundleOptionData
     */
    public function addBundleOption(array $bundleOptionData)
    {
        $productSearch = array();
        $selectionSettings = array();
        $fieldSetXpath = $this->getCurrentLocationUimapPage()->findFieldset('bundle_items')->getXpath();
        $optionsCount = $this->getXpathCount($fieldSetXpath . "//div[@class='option-box']");
        $this->addParameter('optionId', $optionsCount);
        $this->clickButton('add_new_option', FALSE);
        $this->fillForm($bundleOptionData, 'bundle_items');
        foreach ($bundleOptionData as $key => $value) {
            if (preg_match('/^bundle_items_add_product/', $key) and is_array($bundleOptionData[$key])) {
                $this->_prepareDataForSearch($bundleOptionData[$key]);
                if (count($bundleOptionData[$key]) > 0) {
                    $this->clickButton('add_selection', FALSE);
                    $this->pleaseWait();
                    $this->clickButton('reset_filter', FALSE);
                    $this->pleaseWait();
                    foreach ($bundleOptionData[$key] as $k => $v) {
                        if (preg_match('/^bundle_items_/', $k)) {
                            if ($k == 'bundle_items_sku' or $k == 'bundle_items_name') {
                                $this->addParameter('productSku', $v);
                            }
                            if ($k !== 'bundle_items_qty_to_add') {
                                $productSearch[$k] = $v;
                            } else {
                                $selectionSettings['selection_item_default_qty'] = $v;
                            }
                        }
                        if (preg_match('/^selection_item_/', $k)) {
                            $selectionSettings[$k] = $v;
                        }
                    }
                    $this->searchAndChoose($productSearch, 'select_product_to_bundle_option');
                    $this->clickButton('add_selected_products', FALSE);
                    $this->fillForm($selectionSettings, 'new_bundle_option');
                }
            }
        }
    }

    /**
     * Add Sample for Downloadable product
     *
     * @param array $optionData
     * @param string $type
     */
    public function addDownloadableOption(array $optionData, $type)
    {
        $sampleFieldSet = $this->_getControlXpath('link', 'downloadable_' . $type);
        $this->_prepareDataForSearch($optionData);
        if (empty($optionData)) {
            return true;
        }
        if (!$this->isElementPresent($sampleFieldSet . "/parent::*[normalize-space(@class)='open']")) {
            $this->clickControl('link', 'downloadable_' . $type, FALSE);
        }

        $fieldSetXpath = $this->getCurrentLocationUimapPage()->findFieldset('downloadable_' . $type)->getXpath();
        $rowNumber = $this->getXpathCount($fieldSetXpath . "//*[@id='sample_items_body']/tr");
        $this->addParameter('rowId', $rowNumber);
        $this->clickButton('downloadable_' . $type . '_add_new_row', FALSE);
        $this->fillForm($optionData, 'downloadable_information');
    }

    /**
     * Fill user product attribute
     *
     * @param array $productData
     * @param type $tabName
     */
    public function fillUserAttributesOnTab(array $productData, $tabName)
    {
        $userFieldData = $tabName . '_user_attr';
        if (array_key_exists($userFieldData, $productData) && is_array($productData[$userFieldData])) {
            foreach ($productData[$userFieldData] as $fieldType => $dataArray) {
                if (is_array($dataArray)) {
                    foreach ($dataArray as $fieldKey => $fieldValue) {
                        $this->addParameter('attibuteCode' . ucfirst(strtolower($fieldType)), $fieldKey);
                        $xpath = $this->_getControlXpath($fieldType, $tabName . '_user_attr_' . $fieldType);
                        switch ($fieldType) {
                            case 'dropdown':
                                $this->select($xpath, $fieldValue);
                                break;
                            case 'field':
                                $this->type($xpath, $fieldValue);
                                break;
                            case 'multiselect':
                                $this->removeAllSelections($xpath);
                                $values = explode(',', $fieldValue);
                                $values = array_map('trim', $values);
                                foreach ($values as $v) {
                                    $this->addSelection($xpath, $v);
                                }
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Find Column Number in table by Name
     *
     * @param type $columnName
     * @param type $fieldSetName
     * @return int
     */
    public function findColumnNumberByName($columnName, $fieldSetName = null)
    {
        if ($fieldSetName != null) {
            $fieldSetXpath = $this->getCurrentUimapPage()->getMainForm()->findFieldset($fieldSetName)->getXpath();
        } else {
            $fieldSetXpath = '';
        }
        $columnXpath = $fieldSetXpath . "//table[@class='data']//tr[@class='headings']/th";
        $columnQty = $this->getXpathCount($columnXpath);
        for ($i = 1; $i <= $columnQty; $i++) {
            $text = $this->getText($columnXpath . "[$i]");
            if ($text == $columnName) {
                return $i;
            }
        }
        return 0;
    }

    /**
     * Create Product
     *
     * @param array $productData
     * @param string $productType
     */
    public function createProduct(array $productData, $productType='simple')
    {
        $productData = $this->arrayEmptyClear($productData);
        $this->clickButton('add_new_product');
        $this->fillProductSettings($productData, $productType);
        if ($productType == 'configurable') {
            $this->fillConfigurableSettings($productData);
        }
        $this->fillProductInfo($productData, $productType);
        $this->saveForm('save');
    }

    /**
     * Fill Product info
     *
     * @param array $productData
     * @param string $productType
     */
    public function fillProductInfo(array $productData, $productType='simple')
    {
        $this->fillTab($productData);
        $this->fillTab($productData, 'prices');
        $this->fillTab($productData, 'meta_information');
        //@TODO Fill in Images Tab
        if ($productType == 'simple' || $productType == 'virtual') {
            $this->fillTab($productData, 'recurring_profile');
        }
        $this->fillTab($productData, 'design');
        $this->fillTab($productData, 'gift_options');
        $this->fillTab($productData, 'inventory');
        $this->fillTab($productData, 'websites');
        $this->fillTab($productData, 'categories');
        $this->fillTab($productData, 'related_products');
        $this->fillTab($productData, 'up_sells_products');
        $this->fillTab($productData, 'cross_sells_products');
        $this->fillTab($productData, 'custom_options');
        if ($productType == 'grouped' || $productType == 'configurable') {
            $this->fillTab($productData, 'associated_products');
        }
        if ($productType == 'bundle') {
            $this->fillTab($productData, 'bundle_items');
        }
        if ($productType == 'downloadable') {
            $this->fillTab($productData, 'downloadable_information');
        }
    }

    /**
     * Open product.
     *
     * @param array $productSearch
     */
    public function openProduct(array $productSearch)
    {
        $this->assertTrue($this->searchAndOpen($productSearch), 'Product is not found');
    }

}
