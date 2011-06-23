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
                $this->clickControl('tab', $tabName, FALSE);
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
                case 'custom_options':
                    $this->pleaseWait();
                    foreach ($productData as $key => $value) {
                        if (preg_match('/^custom_options/', $key)) {
                            $this->addCustomOption($productData[$key]);
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
        $this->fillTab($productData, 'custom_options');
        //@TODO Fill in Websites Tab
        //@TODO Fill in Categories Tab
        //@TODO Fill in Related Products Tab
        //@TODO Fill in Up-sells Tab
        //@TODO Fill in Cross-sells Tab
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