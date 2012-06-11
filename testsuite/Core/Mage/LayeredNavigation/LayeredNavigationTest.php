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
 * Layered navigation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Core_Mage_LayeredNavigation_LayeredNavigationTest extends Mage_Selenium_TestCase
{


    /**
     * <p>Creating categories and products</p>
     * <p>Steps</p>
     * <p>1. Click "Add Sub category" button </p>
     * <p>2. Fill in required fields and set category as anchor</p>
     * <p>3. Click "Save Category" button</p>
     * <p>4. Select created category</p>
     * <p>5. Click "Add Sub category" button </p>
     * <p>6. Fill in required fields</p>
     * <p>7. Click "Save Category" button</p>
     * <p>8. Navigate to Manage products page</p>
     * <p>9. Create visible on frontend Simple product assigned to category created on 7 step </p>
     * <p>Expected Result:</p>
     * <p>Categories with assigned simple products created</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        //Data
        // Creating anchor category
        $rootCategoryName = 'Default Category';
        $categoryData = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category'=> $rootCategoryName));
        $categoryData['is_anchor'] = 'Yes';
        $this->categoryHelper()->createCategory($categoryData);
        // Creating subcategory
        $categoryName = $rootCategoryName . '/' . $categoryData['name'];
        $subCategoryData = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category'=> $categoryName));
        $this->categoryHelper()->createCategory($subCategoryData);
        $subCategoryName = $categoryName . '/' . $subCategoryData['name'];
        // Creating non-anchor category (due to framework limitation)
        $nonAnchorCategoryData = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category'=> $rootCategoryName));
        $this->categoryHelper()->createCategory($nonAnchorCategoryData);
        $nonAnchorCategoryName = $rootCategoryName . '/' . $nonAnchorCategoryData['name'];
        // Creating subcategory for non-anchor category
        $subCategoryForNonAnchorCategoryData = $this->loadDataSet('Category',
            'sub_category_required', array('parent_category'=> $nonAnchorCategoryName));
        $this->categoryHelper()->createCategory($subCategoryForNonAnchorCategoryData);
        // Creating products
        $this->navigate('manage_products');
        $simple1 = $this->loadDataSet('Product', 'simple_product_visible', array ('categories' => $subCategoryName));
        $this->productHelper()->createProduct($simple1);
        $this->navigate('manage_products');
        $simple2 = $this->loadDataSet('Product', 'simple_product_visible', array ('categories' => $categoryName));
        $this->productHelper()->createProduct($simple2);

        return array('simple1'  => $simple1['general_name'],
            'simple2' => $simple2['general_name'],
            'acategory' => $categoryData['name'],
            'subcategory' => $subCategoryData['name'],
            'nacategory' => $nonAnchorCategoryData['name']);
    }

    /**
     * <p>Checking that layered navigation block present on the anchor category page</p>
     * <p>Steps</p>
     * <p>1. Go to frontend </p>
     * <p>2. Navigate to anchor category </p>
     * <p>3. Check layered navigation block </p>
     * <p>Expected Result:</p>
     * <p>Layered Navigation block should be present on the page</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @test
     * @TestLinkId
     */
    public function checkLayeredNavigationOnAnchorCategoryPage($data)
    {
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['acategory']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('fieldset',
            'layered_navigation_anchor')), 'There is no LN block on the ' . $data['acategory'] . 'anchor category page');
    }

    /**
     * <p>Selecting subcategory in anchor category layered navigation block</p>
     * <p>Steps</p>
     * <p>1. Go to frontend </p>
     * <p>2. Navigate to anchor category </p>
     * <p>3. Click on subcategory in layered navigation block </p>
     * <p>Expected Result:</p>
     * <p>Subcategory selected, products assigned to this subcategory displays in product grid</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     */
    public function selectCategoryAnchor($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['acategory']);
        $this->layeredNavigationHelper()->setCategoryIdFromLink($data['subcategory']);
        $this->clickControl('link', 'category_name');
        //Verification
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('pageelement', 'currently_shopping_by')),
            'There is no currently_shopping_by block in layerd navigation');
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('button', 'remove_this_item')),
            'There is no "remove this item" button');
        $this->assertTrue($this->isElementPresent(
            $this->_getControlXpath('link', 'clear_all')), 'There is no "Clear All" link');
        $this->addParameter('productName', $data['simple1']);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('pageelement', 'product_name_header')),
            'Product assigned to category page displays after filtering');
    }

    /**
     * <p>Removing selected category from the anchor category layered navigation block</p>
     * <p>Steps</p>
     * <p>1. Click on remove_this_item button </p>
     * <p>Expected Result:</p>
     * <p>Subcategory removed from currently_shooping_by block</p>
     *
     * @param $data
     * @depends preconditionsForTests
     * @depends selectCategoryAnchor
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     */
    public function removeSelectedCategoryAnchor($data)
    {
        //Steps
        $this->clickControl('button', 'remove_this_item');
        //Verification
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('button', 'remove_this_item')),
            'remove_this_item button still present in layered navigation block');
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('link', 'clear_all')),
            '"Clear All" link still present in layered navigation block');
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('pageelement', 'currently_shopping_by')),
            'currently_shopping_by block still present in layered navigation block');
        $this->addParameter('productName', $data['simple1']);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no ' . $data['simple1'] . 'product on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no' . $data['simple2'] . 'product on the page');
    }

    /**
     * <p>"Clear All" from the anchor category layered navigation block</p>
     * <p>Steps</p>
     * <p>1. Click on subcategory in layered navigation block </p>
     * <p>2. Click on Clear All link </p>
     * <p>Expected Result:</p>
     * <p>Subcategory removed from currently_shooping_by block</p>
     *
     * @param $data
     * @depends preconditionsForTests
     * @depends selectCategoryAnchor
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     */
    public function removeSelectedCategoryAnchorClearAll($data)
    {
        //Steps
        $this->layeredNavigationHelper()->setCategoryIdFromLink($data['subcategory']);
        $this->clickControl('link', 'category_name');
        //Verification
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('pageelement', 'currently_shopping_by')),
            'There is no currently_shopping_by block in layerd navigation');
        $this->clickControl('link', 'clear_all');
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('button', 'remove_this_item')),
            'remove_this_item button still present in layered navigation block');
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('link', 'clear_all')),
            '"Clear All" link still present in layered navigation block');
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('pageelement', 'currently_shopping_by')),
            'currently_shopping_by block still present in layered navigation block');
        $this->addParameter('productName', $data['simple1']);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no ' . $data['simple1'] . 'product on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no' . $data['simple2'] . 'product on the page');
    }


    /**
     * <p>Checking that layered navigation block present on the non-anchor category page</p>
     * <p>Steps</p>
     * <p>1. Go to frontend </p>
     * <p>2. Navigate to non-anchor category </p>
     * <p>3. Check layered navigation block </p>
     * <p>Expected Result:</p>
     * <p>Layered Navigation block should be present on the page</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @test
     */
    public function checkLayeredNavigationOnNonAnchorCategoryPage($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['nacategory']);
        //Verification
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('fieldset', 'layered_navigation')),
            'There is no LN block on the' . $data['nacategory'] . 'non-anchor category page');
    }
}