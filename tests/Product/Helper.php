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

    public function fillProductSettings($productSettings)
    {
        $this->assertTrue($this->checkCurrentPage('new_product_settings'), 'Wrong page is displayed');
        $this->fillForm($productSettings);
        // Defining and adding %attributeSetID% and %productType% for Uimap pages.
        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('product_settings');
        foreach ($productSettings as $fieldsName => $fieldValue) {
            $xpath = $fieldSet->findDropdown($fieldsName);
            switch ($fieldsName) {
                case 'product_attribute_set':
                    $attributeSetID = $this->getValue($xpath . "/option[text()='$fieldValue']");
                    break;
                case 'product_type':
                    $productType = $this->getValue($xpath . "/option[text()='$fieldValue']");
                    break;
                default:
                    break;
            }
        }
        $this->addParameter('attributeSetID', $attributeSetID);
        $this->addParameter('productType', $productType);
        $page->assignParams($this->_paramsHelper);
        //Steps. Сlick 'Сontinue' button
        $this->clickButton('continue_button');
    }

    public function createSimpleProduct(array $productSettings, array $productData)
    {
        //Steps
        $this->clickButton('add_new_product');
        $this->fillProductSettings($productSettings);
        //Get Uimap page
        $page = $this->getCurrentLocationUimapPage();
        //Fill in General Tab
        $this->fillForm($productData, 'general');
        //Fill in Prices Tab
        $this->clickControl('tab', 'prices', FALSE);
        $fieldSet = $page->findFieldset('product_prices');
        $fieldSetXpath = $fieldSet->getXpath();
        $rowNumber = $this->getXpathCount($fieldSetXpath . "//*[@id='tier_price_container']/tr");
        $this->addParameter('tierPriceId', $rowNumber);
        $page->assignParams($this->_paramsHelper);
        $this->clickButton('add_tier_price', FALSE);
        $this->fillForm($productData, 'prices');
        //fill in meta information tab
        $this->clickControl('tab', 'meta_information', FALSE);
        $this->fillForm($productData, 'meta_information');
        //
        $this->clickControl('tab', 'recurring_profile', FALSE);
        $this->fillForm($productData, 'recurring_profile');
        //
        $this->clickControl('tab', 'design', FALSE);
        $this->fillForm($productData, 'design');
        //
        $this->clickControl('tab', 'gift_options', FALSE);
        $this->fillForm($productData, 'gift_options');
        //
        $this->clickControl('tab', 'inventory', FALSE);
        $this->fillForm($productData, 'inventory');
        //
//        $this->clickControl('tab', 'custom_options', FALSE);
//        $this->pleaseWait();
//        $fieldSet = $page->findFieldset('product_custom_options');
//        $fieldSetXpath = $fieldSet->getXpath();
//        $optionCount = $this->getXpathCount($fieldSetXpath . "//*[@class='option-box']") + 1;
//        $this->addParameter('optionId', $optionCount);
//        $page->assignParams($this->_paramsHelper);
//        $this->clickButton('add_new_option', FALSE);
//        $this->fillForm($productData, 'custom_options');
        //save
        //$this->saveForm('save');
    }

    public function createBundleProduct(array $productSettings, array $productData)
    {
        //Steps
        $this->clickButton('add_new_product');
        $this->fillProductSettings($productSettings);
        //Get Uimap page
        $page = $this->getCurrentLocationUimapPage();
        //Fill in General Tab
        $this->fillForm($productData, 'general');
        //Fill in Prices Tab
        $this->clickControl('tab', 'prices', FALSE);
        $fieldSet = $page->findFieldset('product_prices');
        $fieldSetXpath = $fieldSet->getXpath();
        $rowNumber = $this->getXpathCount($fieldSetXpath . "//*[@id='tier_price_container']/tr");
        $this->addParameter('tierPriceId', $rowNumber);
        $page->assignParams($this->_paramsHelper);
        $this->clickButton('add_tier_price', FALSE);
        $this->fillForm($productData, 'prices');
        //fill in meta information tab
        $this->clickControl('tab', 'meta_information', FALSE);
        $this->fillForm($productData, 'meta_information');
        //
        $this->clickControl('tab', 'design', FALSE);
        $this->fillForm($productData, 'design');
        //
        $this->clickControl('tab', 'gift_options', FALSE);
        $this->fillForm($productData, 'gift_options');
        //
        $this->clickControl('tab', 'inventory', FALSE);
        $this->fillForm($productData, 'inventory');
        //
//        $this->clickControl('tab', 'custom_options', FALSE);
//        $this->pleaseWait();
//        $fieldSet = $page->findFieldset('product_custom_options');
//        $fieldSetXpath = $fieldSet->getXpath();
//        $optionCount = $this->getXpathCount($fieldSetXpath . "//*[@class='option-box']") + 1;
//        $this->addParameter('optionId', $optionCount);
//        $page->assignParams($this->_paramsHelper);
//        $this->clickButton('add_new_option', FALSE);
//        $this->fillForm($productData, 'custom_options');
        //save
        //$this->saveForm('save');
    }

}