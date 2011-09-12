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
class Category_Create_SubCategoryTest extends Mage_Selenium_TestCase
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
        //$this->assertTrue($this->checkCurrentPage('manage_categories'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * <p>Creating Subcategory with required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @test
     */
    public function subCategoryWithRequiredFieldsOnly()
    {
        //Data
        $categoryData = $this->loadData('sub_category_required', null, 'name');
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        return($categoryData['name']);
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating Subcategory with required fields empty</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in necessary fields, leave required fields empty</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory not created, error message appears</p>
     *
     * @dataProvider dataEmptyFields
     * @test
     */
    public function subCategoryWithRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $overrideData = array($emptyField => '%noValue%');
        $categoryData = $this->loadData('sub_category_required', $overrideData);
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function dataEmptyFields()
    {
        return array(
            array('name', 'field'),
            array('available_product_listing', 'multiselect')
        );
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating Subcategory with special characters</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields with special characters</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @test
     */
    public function subCategoryWithSpecialCharacters()
    {
        //Data
        $categoryData = $this->loadData('sub_category_required',
                array('name' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating Subcategory with long values in required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @test
     */
    public function subCategoryWithLongValues()
    {
        //Data
        $categoryData = $this->loadData('sub_category_required',
                array('name' => $this->generate('string', 255, ':alnum:')));
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating nested Subcategory with required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2.Select existing "Category Path"</p>
     * <p>3. Fill in required fields</p>
     * <p>4. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @depends subCategoryWithRequiredFieldsOnly
     * @test
     */
    public function nestedSubCategory($categoryPath)
    {
        //Data
        $categoryDataNested = $this->loadData('sub_category_required', null, 'name');
        $categoryPath = 'Default Category/'. $categoryPath;
        //Steps
        $this->categoryHelper()->createSubCategory($categoryPath, $categoryDataNested);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }
}
