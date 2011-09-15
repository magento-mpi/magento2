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
class Category_DeleteTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Categories</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_categories');
        $this->assertTrue($this->checkCurrentPage('manage_categories'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * <p>Deleting Root Category</p>
     * <p>Pre-Conditions:</p>
     * <p>Root Category created</p>
     * <p>Steps:</p>
     * <p>Select Root Category</p>
     * <p>Click "Delete" button</p>
     * <p>Expected result</p>
     * <p>Root category Deleted, Success message appears</p>
     *
     * @test
     */
    public function deleteRootCategory()
    {
        //Data
        $rootCategoryData = $this->loadData('root_category', null, 'name');
        //Steps
        $this->categoryHelper()->createRootCategory($rootCategoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->clickButton('delete_category', FALSE);
        $this->pleaseWait(2);
        $this->assertTrue($this->successMessage('success_deleted_category'), $this->messages);
    }

    /**
     * <p>Deleting  Subcategory</p>
     * <p>Pre-Conditions:</p>
     * <p>Subcategory created</p>
     * <p>Steps:</p>
     * <p>Select created Subcategory</p>
     * <p>Click "Delete" button</p>
     * <p>Expected result</p>
     * <p>Subcategory Deleted, Success message appears</p>
     *
     *
     * @test
     */
    public function deleteSubCategory()
    {
        //Data
        $subCategoryData = $this->loadData('sub_category_required', null, 'name');
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $subCategoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->clickButton('delete_category', FALSE);
        $this->pleaseWait(2);
        $this->assertTrue($this->successMessage('success_deleted_category'), $this->messages);
    }

    /**
     * <p>Deleting Root Category that assigned to store</p>
     * <p>Pre-Conditions:</p>
     * <p>Root Category created and assigned to store</p>
     * <p>Steps:</p>
     * <p>Select Root Category</p>
     * <p>Expected result</p>
     * <p>Verify that button "Delete" is absent on the page</p>
     *
     * @test
     */
    public function rootCategoryThatCannotBeDeleted()
    {
        //Data
        $rootCategoryData = $this->loadData('root_category', null, 'name');
        $storeData = $this->loadData('generic_store', array(
                                     'root_category' => $rootCategoryData['name']), 'store_name');
        //Steps
        $this->categoryHelper()->createRootCategory($rootCategoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        //Pre-Conditions
        $this->navigate('manage_stores');
        $this->assertTrue($this->checkCurrentPage('manage_stores'), 'Wrong page is opened');
        $this->addParameter('id', '0');
        $this->storeHelper()->createStore($storeData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_store'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'), $this->messages);
        //Verifying
        $this->navigate('manage_categories');
        $this->categoryHelper()->defineCorrectCategory($rootCategoryData['name']);
        $this->verifyElementPresent('delete');
    }

    /**
     * <p>Deleting Root Category with Subcategory</p>
     * <p>Pre-Conditions:</p>
     * <p>Root Category Created</p>
     * <p>Subcategory created</p>
     * <p>Steps:</p>
     * <p>Select created Root Category</p>
     * <p>Click "Delete" button</p>
     * <p>Expected result</p>
     * <p>Subcategory Deleted, Success message appears</p>
     *
     * @test
     */
    public function deleteRootCategoryWithSubcategories()
    {
        //Data
        $rootCategoryData = $this->loadData('root_category', null, 'name');
        $subCategoryData = $this->loadData('sub_category_required', null, 'name');
        //Steps
        $this->categoryHelper()->createRootCategory($rootCategoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->categoryHelper()->createSubCategory($rootCategoryData['name'], $subCategoryData);
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->clickButton('reset');
        $this->categoryHelper()->selectCategory($rootCategoryData['name']);
        $this->clickButton('delete_category', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->successMessage('success_deleted_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * <p>Deleting Root Category with Subcategory</p>
     * <p>Pre-Conditions:</p>
     * <p>Root Category Created</p>
     * <p>Subcategory created</p>
     * <p>Steps:</p>
     * <p>Select created Root Category</p>
     * <p>Click "Delete" button</p>
     * <p>Expected result</p>
     * <p>Subcategory Deleted, Success message appears</p>
     *
     * @test
     */
    public function deleteRootCategoryWithSubcategoriesHavingProducts()
    {
        //Data
        $rootCategoryData = $this->loadData('root_category', null, 'name');
        $subCategoryData = $this->loadData('sub_category_with_products', null, 'name');
        $productData = $this->loadData('simple_product_required', null, array('general_name', 'general_sku'));

        $subCategoryData['category_products_data'] = array('product_1' => array(
                'category_products_search_category_products_sku' => $productData['general_sku']));
        //Pre-Conditions
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');

        $this->productHelper()->createProduct($productData);
        //Steps
        $this->assertPreConditions();
        $this->categoryHelper()->createRootCategory($rootCategoryData);
        //Verifying
        $this->pleaseWait();
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->categoryHelper()->createSubCategory($rootCategoryData['name'], $subCategoryData);
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->clickButton('reset');
        $this->categoryHelper()->selectCategory($rootCategoryData['name']);
        $this->clickButton('delete_category', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->successMessage('success_deleted_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }

}
