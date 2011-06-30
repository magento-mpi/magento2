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
     * @param type $productSettings
     */
    public function fillProductSettings($productSettings)
    {
        $this->assertTrue($this->checkCurrentPage('new_product_settings'), 'Wrong page is displayed');
        $this->fillForm($productSettings);
        // Defining and adding %attributeSetID% and %productType% for Uimap pages.
        foreach ($productSettings as $fieldsName => $fieldValue) {
            $xpath = $this->_getControlXpath('dropdown', $fieldsName);
            if ($fieldsName == 'product_attribute_set') {
                $attributeSetID = $this->getValue($xpath . "/option[text()='" . $fieldValue . "']");
                $this->addParameter('attributeSetID', $attributeSetID);
            }
            if ($fieldsName == 'product_type') {
                $productType = $this->getValue($xpath . "/option[text()='" . $fieldValue . "']");
                $this->addParameter('productType', $productType);
            }
            $this->getCurrentLocationUimapPage()->assignParams($this->_paramsHelper);
        }
        //Click 'Comtinue' button
        $this->clickButton('continue_button');
    }

    public function fillTab(array $productData, $tabName = 'general')
    {
        $needFilling = FALSE;
        $waitAjax = False;
        foreach ($productData as $key => $value) {
            if (preg_match('/^' . $tabName . '/', $key) and $value !== '%noValue%') {
                $needFilling = TRUE;
                break;
            }
        }
        if ($needFilling) {
            $tabXpath = $this->getCurrentLocationUimapPage()->findTab($tabName)->getXpath();
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
                    foreach ($productData as $key => $value) {
                        if (preg_match('/^prices_tier_price/', $key) and is_array($productData[$key])) {
                            $this->addTierPrice($productData[$key]);
                        }
                    }
                    $this->fillForm($productData, 'prices');
                    break;
                case 'websites':
                    $valuesArray = explode(',', $productData['websites']);
                    $valuesArray = array_map('trim', $valuesArray);
                    foreach ($valuesArray as $value) {
                        $this->selectWebsite($value);
                    }
                    break;
                case 'related_products': case 'up_sells_products': case 'cross_sells_products':
                    foreach ($productData as $key => $value) {
                        if (preg_match('/^' . $tabName . '/', $key) and is_array($productData[$key])) {
                            $this->assignProduct($productData[$key], $tabName);
                        }
                    }
                    break;
                case 'custom_options':
                    foreach ($productData as $key => $value) {
                        if (preg_match('/^custom_options/', $key) and is_array($productData[$key])) {
                            $this->addCustomOption($productData[$key]);
                        }
                    }
                    break;
                case 'bundle_items':
                    foreach ($productData as $key => $value) {
                        if ($key == 'bundle_items_data' and is_array($productData[$key])) {
                            $this->fillForm($productData[$key], 'bundle_items');
                            foreach ($productData[$key] as $k => $v) {
                                if (preg_match('/^bundle_items/', $k) and is_array($productData[$key][$k])) {
                                    $this->addBundelOption($productData[$key][$k]);
                                }
                            }
                        }
                    }
                    break;
                default:
                    $this->fillForm($productData, $tabName);
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
    public function assignProduct(array $data, $tabName)
    {
        $fieldSetXpath = $this->getCurrentLocationUimapPage()->findFieldset($tabName)->getXpath();
        $setPosition = FALSE;
        if (isset($data[$tabName . '_position']) and $data[$tabName . '_position'] !== '%noValue%') {
            $positionValue = $data[$tabName . '_position'];
            unset($data[$tabName . '_position']);
            $setPosition = True;
        }
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->searchAndChoose($data, $tabName);
        if ($setPosition) {
            // Forming xpath for string that contains the lookup data
            $xpathTR = $fieldSetXpath . "//tr";
            foreach ($data as $key => $value) {
                if (!preg_match('/_from/', $key) and !preg_match('/_to/', $key) and $value != '%noValue%') {
                    $xpathTR .= "[contains(.,'$value')]";
                }
            }
            $productpositionXpath = $this->_getControlXpath('field', $tabName . '_position');
            $this->type($xpathTR . $productpositionXpath, $positionValue);
        }
    }

    /**
     * Add Bundel Option
     *
     * @param array $bundelOptionData
     */
    public function addBundelOption(array $bundelOptionData)
    {
        $productSearch = array();
        $selectionSettings = array();
        $fieldSetXpath = $this->getCurrentLocationUimapPage()->findFieldset('bundle_items')->getXpath();
        $optionsCount = $this->getXpathCount($fieldSetXpath . "//div[@class='option-box']");
        $this->addParameter('optionId', $optionsCount);
        $this->clickButton('add_new_option', FALSE);
        $this->fillForm($bundelOptionData, 'bundle_items');
        foreach ($bundelOptionData as $key => $value) {
            if (preg_match('/^bundle_items_add_product/', $key) and is_array($bundelOptionData[$key])) {
                $this->_prepareDataForSearch($bundelOptionData[$key]);
                if (count($bundelOptionData[$key]) > 0) {
                    $this->clickButton('add_selection', FALSE);
                    $this->pleaseWait();
                    $this->clickButton('reset_filter', FALSE);
                    $this->pleaseWait();
                    foreach ($bundelOptionData[$key] as $k => $v) {
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
     * Fill Base Tabs
     *
     * @param array $productData
     */
    public function fillBaseTabs(array $productData)
    {
        $this->fillTab($productData);
        $this->fillTab($productData, 'prices');
        $this->fillTab($productData, 'meta_information');
        //@TODO Fill in Images Tab
        $this->fillTab($productData, 'recurring_profile');
        $this->fillTab($productData, 'design');
        $this->fillTab($productData, 'gift_options');
        $this->fillTab($productData, 'inventory');
        $this->fillTab($productData, 'websites');
        //@TODO Fill in Categories Tab
        $this->fillTab($productData, 'related_products');
        $this->fillTab($productData, 'up_sells_products');
        $this->fillTab($productData, 'cross_sells_products');
        $this->fillTab($productData, 'custom_options');
    }

    /**
     * Create Product
     * @param type $productData
     */
    public function createProduct(array $productSettings, array $productData)
    {
        $this->clickButton('add_new_product');
        $this->fillProductSettings($productSettings);
        $this->fillBaseTabs($productData);
        $this->saveForm('save');
    }

}