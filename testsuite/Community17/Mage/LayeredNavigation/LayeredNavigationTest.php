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

class Community17_Mage_LayeredNavigation_LayeredNavigationTest extends Mage_Selenium_TestCase
{


    /**
     * <p>Creating categories and products</p>
     * <p>Steps</p>
     * <p>1. Login to backend
     * <p>2. Create anchor category
     * <p>3. Create subcategory under anchor category
     * <p>4. Create non-anchor category
     * <p>5. Create subcategory under non-anchor category
     * <p>6. Create simple product assigned to subcategory under anchor category
     * <p>7. Create simple product assigned anchor category
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
        // Creating anchor category
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        $categoryData['is_anchor'] = 'Yes';
        $rootCategoryName = $categoryData['parent_category'];
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        // Creating subcategory for anchor category
        $categoryName = $rootCategoryName . '/' . $categoryData['name'];
        $subCategoryData = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category' => $categoryName));
        $this->categoryHelper()->createCategory($subCategoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $subCategoryName = $categoryName . '/' . $subCategoryData['name'];

        // Creating non-anchor category
        $nonAnchorCategoryData = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category' => $rootCategoryName));
        $this->categoryHelper()->createCategory($nonAnchorCategoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $nonAnchorCategoryName = $rootCategoryName . '/' . $nonAnchorCategoryData['name'];

        // Creating subcategory for non-anchor category
        $subCategoryForNonAnchorCategoryData = $this->loadDataSet('Category',
            'sub_category_required', array('parent_category' => $nonAnchorCategoryName));
        $this->categoryHelper()->createCategory($subCategoryForNonAnchorCategoryData);
        $this->assertMessagePresent('success', 'success_saved_category');

        // Creating attributes
        $this->navigate('manage_attributes');
        $dropdownAttrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $dropdownOption1 = $dropdownAttrData['option_1']['admin_option_name'];
        $this->productAttributeHelper()->createAttribute($dropdownAttrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');

        $multiselectAttrData = $this->loadDataSet('ProductAttribute', 'product_attribute_multiselect_with_options');
        $multiselectOption2 = $multiselectAttrData['option_2']['admin_option_name'];
        $this->productAttributeHelper()->createAttribute($multiselectAttrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');

        $priceAttrData = $this->loadDataSet('ProductAttribute', 'product_attribute_price');
        $this->productAttributeHelper()->createAttribute($priceAttrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');

        // Creating attribute set
        $this->navigate('manage_attribute_sets');
        $attr = array($dropdownAttrData['attribute_code'], $multiselectAttrData['attribute_code'],
            $priceAttrData['attribute_code']);
        $attrSetData = $this->loadDataSet('AttributeSet', 'attribute_set',
            array('associated_attributes' => array('General' => $attr)));
        $this->attributeSetHelper()->createAttributeSet($attrSetData);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        // Creating products
        $this->navigate('manage_products');
        $simpleProduct1WithAttributes = $this->loadDataSet('Product', 'simple_product_visible_with_user_attributes',
            array('categories' => $subCategoryName, 'product_attribute_set' => $attrSetData['set_name'],
                'general_user_attr_dropdown' => $dropdownOption1,
                'general_user_attr_multiselect' => $multiselectOption2));
        $this->addParameter('attributeCodeDropdown', $dropdownAttrData['attribute_code']);
        $this->addParameter('attributeCodeMultiselect', $multiselectAttrData['attribute_code']);
        $this->addParameter('attributeCodeField', $priceAttrData['attribute_code']);
        $this->productHelper()->createProduct($simpleProduct1WithAttributes);
        $this->assertMessagePresent('success', 'success_saved_product');

        $simpleProduct2 = $this->loadDataSet('Product', 'simple_product_visible', array('categories' => $categoryName));
        $this->productHelper()->createProduct($simpleProduct2);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'simple1attr' => $simpleProduct1WithAttributes['general_name'],
            'simple1dropdownOptionName' => $dropdownAttrData['option_1']['store_view_titles']['Default Store View'],
            'simple1dropdownCode' => $dropdownAttrData['attribute_code'],
            'simple1mselectOptionName' => $multiselectAttrData['option_2']['store_view_titles']['Default Store View'],
            'simple1mselectCode' => $multiselectAttrData['attribute_code'],
            'simple1priceAttr' => $priceAttrData['attribute_code'],
            'simple2' => $simpleProduct2['general_name'],
            'acategory' => $categoryData['name'],
            'subcategory' => $subCategoryData['name'],
            'nacategory' => $nonAnchorCategoryData['name']);
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
     * @TestlinkId TL-MAGE-5610
     */
    public function checkLayeredNavigationOnNonAnchorCategoryPage($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['nacategory']);
        //Verifying
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('fieldset', 'layered_navigation')),
            'There is no LN block on the' . $data['nacategory'] . 'non-anchor category page');
    }

    /**
     * <p>Checking that layered navigation block present on the anchor category page</p>
     * <p>Steps</p>
     * <p>1. Go to frontend </p>
     * <p>2. Navigate to anchor category </p>
     * <p>3. Check layered navigation block </p>
     * <p>Expected Result:</p>
     * <p>Layered Navigation block (with link to subcategory) should be present on the page</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-5606
     */
    public function checkLayeredNavigationOnAnchorCategoryPage($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['acategory']);
        //Verifying
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('fieldset',
            'layered_navigation_anchor')), 'There is no LN block on the ' . $data['acategory'] . 'anchor category');
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
     * @TestlinkId TL-MAGE-5607
     */
    public function selectCategoryAnchor($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['acategory']);
        $this->layeredNavigationHelper()->setCategoryIdFromLink($data['subcategory']);
        $this->clickControl('link', 'category_name');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertFalse($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'Product assigned to category page displays after filtering');
    }

    /**
     * <p>Removing selected category from the anchor category layered navigation block using Remove button</p>
     * <p>Steps</p>
     * <p>1. Click on remove_this_item button </p>
     * <p>Expected Result:</p>
     * <p>Subcategory removed from currently_shooping_by block</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @depends selectCategoryAnchor
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     * @TestlinkId TL-MAGE-5608
     */
    public function removeSelectedCategoryAnchor($data)
    {
        //Steps
        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no ' . $data['simple1attr'] . 'product on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no' . $data['simple2'] . 'product on the page');
    }

    /**
     * <p>Removing selected category from the anchor category layered navigation block using Clear All link</p>
     * <p>Steps</p>
     * <p>1. Click on subcategory in layered navigation block </p>
     * <p>2. Click on Clear All link </p>
     * <p>Expected Result:</p>
     * <p>Subcategory removed from currently_shooping_by block</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @depends selectCategoryAnchor
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     * @TestlinkId TL-MAGE-5609
     */
    public function removeSelectedCategoryAnchorClearAll($data)
    {
        //Steps
        $this->layeredNavigationHelper()->setCategoryIdFromLink($data['subcategory']);
        $this->clickControl('link', 'category_name');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'currently_shopping_by')),
            'There is no currently_shopping_by block in layerd navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no ' . $data['simple1attr'] . 'product on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no' . $data['simple2'] . 'product on the page');
    }

    /**
     * <p>Selecting dropdown attribute</p>
     * <p>Steps</p>
     * <p>1. Go to frontend </p>
     * <p>2. Navigate to anchor category </p>
     * <p>3. Click on dropdown attribute in layered navigation block </p>
     * <p>Expected Result:</p>
     * <p>Only product with selected dropdown attribute displays in product grid</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-5649
     */
    public function selectDropdownAttribute($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['acategory']);
        $this->layeredNavigationHelper()->setAttributeIdFromLink($data['acategory'], $data['simple1dropdownCode'],
            $data['simple1dropdownOptionName']);
        $this->clickControl('link', 'attribute_name');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertFalse($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'Product assigned to category page displays after filtering');
    }

    /**
     * <p>Removing selected dropdown attribute using Remove button</p>
     * <p>Steps</p>
     * <p>1. Click on remove_this_item button </p>
     * <p>Expected Result:</p>
     * <p>Dropdown attribute removed from currently_shooping_by block</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @depends selectDropdownAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     * @TestlinkId TL-MAGE-5650
     */
    public function removeSelectedDropdown($data)
    {
        //Steps
        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no ' . $data['simple1attr'] . 'product on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no' . $data['simple2'] . 'product on the page');
    }

    /**
     * <p>Removing selected drodown attribute using Clear All link</p>
     * <p>Steps</p>
     * <p>1. Click on dropdown attribute in layered navigation block </p>
     * <p>2. Click on Clear All link </p>
     * <p>Expected Result:</p>
     * <p>Dropdown attribute removed from currently_shooping_by block</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @depends selectDropdownAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     * @TestlinkId TL-MAGE-5651
     */
    public function removeDropdownAttributeClearAll($data)
    {
        //Steps
        $this->layeredNavigationHelper()->setAttributeIdFromLink($data['acategory'], $data['simple1dropdownCode'],
            $data['simple1dropdownOptionName']);
        $this->clickControl('link', 'attribute_name');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'currently_shopping_by')),
            'There is no currently_shopping_by block in layerd navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no ' . $data['simple1attr'] . 'product on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no' . $data['simple2'] . 'product on the page');
    }

    /**
     * <p>Selecting multiselect attribute</p>
     * <p>Steps</p>
     * <p>1. Go to frontend </p>
     * <p>2. Navigate to anchor category </p>
     * <p>3. Click on multiselect attribute in layered navigation block </p>
     * <p>Expected Result:</p>
     * <p>Only product with selected multiselect attribute displays in product grid</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-5649
     */
    public function selectMultiselectAttribute($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['acategory']);
        $this->layeredNavigationHelper()->setAttributeIdFromLink($data['acategory'], $data['simple1mselectCode'],
            $data['simple1mselectOptionName']);
        $this->clickControl('link', 'attribute_name');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertFalse($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'Product assigned to category page displays after filtering');
    }

    /**
     * <p>Removing selected multiselect attribute using Remove button</p>
     * <p>Steps</p>
     * <p>1. Click on remove_this_item button </p>
     * <p>Expected Result:</p>
     * <p>multiselect attribute removed from currently_shooping_by block</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @depends selectMultiselectAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     * @TestlinkId TL-MAGE-5650
     */
    public function removeSelectedMultiselect($data)
    {
        //Steps
        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no ' . $data['simple1attr'] . 'product on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no' . $data['simple2'] . 'product on the page');
    }

    /**
     * <p>Removing selected drodown attribute using Clear All link</p>
     * <p>Steps</p>
     * <p>1. Click on dropdown attribute in layered navigation block </p>
     * <p>2. Click on Clear All link </p>
     * <p>Expected Result:</p>
     * <p>Dropdown attribute removed from currently_shooping_by block</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @depends selectMultiselectAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     * @TestlinkId TL-MAGE-5651
     */
    public function removeMultiselectAttributeClearAll($data)
    {
        //Steps
        $this->layeredNavigationHelper()->setAttributeIdFromLink($data['acategory'], $data['simple1mselectCode'],
            $data['simple1mselectOptionName']);
        $this->clickControl('link', 'attribute_name');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'currently_shopping_by')),
            'There is no currently_shopping_by block in layerd navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no ' . $data['simple1attr'] . 'product on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no' . $data['simple2'] . 'product on the page');
    }

    /**
     * <p>Selecting price attribute</p>
     * <p>Steps</p>
     * <p>1. Go to frontend </p>
     * <p>2. Navigate to anchor category </p>
     * <p>3. Click on price attribute in layered navigation block </p>
     * <p>Expected Result:</p>
     * <p>Only product with selected price attribute displays in product grid</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @test
     * @TestlinkId TL-MAGE-5649
     */
    public function selectPriceAttribute($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['acategory']);
        $this->layeredNavigationHelper()->setAttributeIdFromLink($data['acategory'], $data['simple1mselectCode']);
        $this->clickControl('link', 'price_attribute');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertFalse($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'Product assigned to category page displays after filtering');
    }

    /**
     * <p>Removing selected price attribute using Remove button</p>
     * <p>Steps</p>
     * <p>1. Click on remove_this_item button </p>
     * <p>Expected Result:</p>
     * <p>price attribute removed from currently_shooping_by block</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @depends selectPriceAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     * @TestlinkId TL-MAGE-5650
     */
    public function removeSelectedPriceAttrbute($data)
    {
        //Steps
        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no ' . $data['simple1attr'] . 'product on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no' . $data['simple2'] . 'product on the page');
    }

    /**
     * <p>Removing selected price attribute using Clear All link</p>
     * <p>Steps</p>
     * <p>1. Click on dropdown attribute in layered navigation block </p>
     * <p>2. Click on Clear All link </p>
     * <p>Expected Result:</p>
     * <p>Price attribute removed from currently_shooping_by block</p>
     *
     * @param array $data
     * @depends preconditionsForTests
     * @depends selectPriceAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @test
     * @TestlinkId TL-MAGE-5651
     */
    public function removePriceAttributeClearAll($data)
    {
        //Steps
        $this->layeredNavigationHelper()->setAttributeIdFromLink($data['acategory'], $data['simple1mselectCode']);
        $this->clickControl('link', 'price_attribute');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'currently_shopping_by')),
            'There is no currently_shopping_by block in layerd navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simple1attr']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no ' . $data['simple1attr'] . 'product on the page');
        $this->addParameter('productName', $data['simple2']);
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'product_name_header')),
            'There is no' . $data['simple2'] . 'product on the page');
    }
}