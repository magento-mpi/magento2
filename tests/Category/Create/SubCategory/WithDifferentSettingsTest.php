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
class Category_Create_SubCategory_WithDifferentSettingsTest extends Mage_Selenium_TestCase
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
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_categories');
        $this->assertTrue($this->checkCurrentPage('manage_categories'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * <p>Creating Subcategory with image</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Fill "Thumbnail Image" and "Image" fields</p>
     * <p>4. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @test
     */
    public function createSubCategoryWithImage()
    {
        //Data
        $categoryData = $this->loadData('root_category',
                array('thumbnail_image' => 'C:\\example.jpg',
            'image' => 'C:\\example.jpg'), 'name');
        $categoryDataSearch['name'] = $categoryData['name'];
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->categoryHelper()->defineCorrectCategory($categoryDataSearch);
        $xpath = $this->_getControlXpath('field', 'name');
        $setValue = $this->getValue($xpath);
        $this->assertEquals($setValue, $categoryData['name'], 'Attribute name should be equal');
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating Subcategory with description</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Fill "Description" field</p>
     * <p>4. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @test
     */
    public function createSubCategoryWithDescription()
    {
        //Data
        $categoryData = $this->loadData('root_category', array('description' => 'SubCategory Description'), 'name');
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating Subcategory with Page Title</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Fill "Page Title" field</p>
     * <p>4. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @test
     */
    public function createSubCategoryWithPageTitle()
    {
        //Data
        $categoryData = $this->loadData('root_category', array('page_title' => 'SubCategory Page Title'), 'name');
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating Subcategory with Include In Navigation Menu - Yes</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Check checkbox in "Include In Navigation Menu" field</p>
     * <p>4. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @test
     */
    public function createSubCategoryWithIncludeInNavigationMenuYes()
    {
        //Data
        $categoryData = $this->loadData('root_category', array('include_in_navigation_menu' => 'Yes'), 'name');
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating Subcategory with Include In Navigation Menu - No</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Uncheck checkbox in "Include In Navigation Menu" field</p>
     * <p>4. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     *
     * @test
     */
    public function createSubCategoryWithIncludeInNavigationMenuNo()
    {
        //Data
        $categoryData = $this->loadData('root_category', array('include_in_navigation_menu' => 'No'), 'name');
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating Subcategory with Is Anchor - Yes</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Check checkbox in "Is Anchor" field</p>
     * <p>4. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @test
     */
    public function createSubCategoryWithIsAnchorYes()
    {
        //Data
        $categoryData = $this->loadData('root_category', array('is_anchor' => 'Yes'), 'name');
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating Subcategory with Is Anchor - No</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Uncheck checkbox in "Is Anchor" field</p>
     * <p>4. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @test
     */
    public function createSubCategoryWithIsAnchorNo()
    {
        //Data
        $categoryData = $this->loadData('root_category', array('is_anchor' => 'No'), 'name');
        //Steps
        $this->categoryHelper()->createSubCategory('Default Category', $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
    }

}
