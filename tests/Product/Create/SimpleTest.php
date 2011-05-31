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
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_Create_SimpleTest extends Mage_Selenium_TestCase
{

    /**
     * Log in to Backend.
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * Preconditions:
     * Navigate to System -> Manage Products
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
    }

    /**
     * @TODO
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $productSettings = $this->loadData('product_create_settings_simple');
        $productData = $this->loadData('simple_product', NULL, 'product_sku');

        //Steps. Open 'Manage Products' page, click 'Add New Product' button, fill in form.
        $this->navigate('manage_products');
        $this->clickButton('add_new_product');
        $this->assertTrue($this->checkCurrentPage('new_product_settings'), 'Wrong page is displayed');
        $this->fillForm($productSettings);
        // Defining and adding %attributeSetID% and %productType% for Uimap pages.
        $page = $this->getCurrentUimapPage();
        $fieldSet = $page->findFieldset('product_settings');
        foreach ($productSettings as $fieldsName => $fieldValue) {
            $xpath = $fieldSet->findDropdown($fieldsName);
            switch ($fieldsName) {
                case 'attribute_set':
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
        //Steps. Сlick 'Сontinue' button
        $this->clickButton('continue_button');
        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);
        $this->fillForm($productData, 'prices');
        $this->clickControl('tab', 'meta_information', FALSE);
        $this->fillForm($productData, 'meta_information');
        $this->clickControl('tab', 'design', FALSE);
        $this->fillForm($productData, 'design');
        $this->clickControl('tab', 'inventory', FALSE);
        $this->fillForm($productData, 'inventory');
        $this->saveForm('save');
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
    }

//    /**
//     * @TODO
//     */
//    public function test_WithSkuThatAlreadyExists()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithRequiredFieldsEmpty()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithSpecialCharacters()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithInvalidValueForFields_InvalidWeight()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithInvalidValueForFields_InvalidPrice()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithInvalidValueForFields_InvalidQty()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithCustomOptions_EmptyFields()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithCustomOptions_InvalidValues()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithSpecialPrice_EmptyValue()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithSpecialPrice_InvalidValue()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithTierPrice_EmptyFields()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_WithTierPrice_InvalidValues()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_OnConfigurableProductPage_QuickCreate()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_OnConfigurableProductPage_CreateEmpty()
//    {
//        // @TODO
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_OnConfigurableProductPage_CopyFromConfigurable()
//    {
//        // @TODO
//    }
}
