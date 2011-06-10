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
class Product_Create_Bundle_DynamicTest extends Mage_Selenium_TestCase
{

    /**
     * Login to backend
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * Preconditions
     * Navigate to Catalog->Manage Products
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is displayed');
        $this->addParameter('id', '0');
    }

    public function test_CreateBundleDynamicProduct()
    {
        //Data
        $productSettings = $this->loadData('product_create_settings_bundle');
        $productData = $this->loadData('bundle_dynamic_product', NULL, 'product_sku');

        //Steps.
        $this->productHelper()->createBundleProduct($productSettings, $productData);
        $this->clickControl('tab', 'bundle_items', FALSE);
        $this->pleaseWait();
        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('bundle_items');
        $fieldSetXpath = $fieldSet->getXpath();
        $rowNumber = $this->getXpathCount($fieldSetXpath . "//*[@class='option-box']");
        $this->addParameter('itemID', $rowNumber);
        $this->clickButton('add_new_option', FALSE);
        $this->fillForm($productData, 'bundle_items');
        $this->saveForm('save');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
    }

//    /**
//     * Steps:
//     *
//     * 1. Login to Admin Page;
//     *
//     * 2. Goto Catalog -> Manage Products;
//     *
//     * 3. Click "Add product" button;
//     *
//     * 4. Fill in "Attribute Set" and "Product Type" fields;
//     *
//     * 5. Click "Continue" button;
//     *
//     * 6. Fill in required fields on General tab;
//     *
//     * 7. Fill in required fields on Prices tab;
//     *
//     * 8. Click "Save" button;
//     *
//     * 9. Verify confirmation message;
//     *
//     * Expected result:
//     *
//     * Product created, confirmation message appears;
//     *
//     * @depends test_CreateSimpleProduct
//     */
//    public function test_WithRequiredFieldsOnly()
//    {
//        // @TODO
//    }
//
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
}
