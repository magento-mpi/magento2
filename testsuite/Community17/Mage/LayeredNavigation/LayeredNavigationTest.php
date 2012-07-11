<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_LayeredNavigation
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
        //Data
        $anchorCategory = $this->loadDataSet('Category', 'sub_category_required');
        $anchorCategory['is_anchor'] = 'Yes';
        $anchorCategoryPath = $anchorCategory['parent_category'] . '/' . $anchorCategory['name'];
        $anchorSubCategory =
            $this->loadDataSet('Category', 'sub_category_required', array('parent_category' => $anchorCategoryPath));
        $nonAnchorCategory = $this->loadDataSet('Category', 'sub_category_required');
        $nonAnchorCategoryPath = $nonAnchorCategory['parent_category'] . '/' . $nonAnchorCategory['name'];
        $nonAnchorSubCategory =
            $this->loadDataSet('Category', 'sub_category_required', array('parent_category' => $nonAnchorCategoryPath));
        $dropdown = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $multiselect = $this->loadDataSet('ProductAttribute', 'product_attribute_multiselect_with_options');
        $price = $this->loadDataSet('ProductAttribute', 'product_attribute_price');
        $attributes = array($dropdown['attribute_code'], $multiselect['attribute_code'], $price['attribute_code']);
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set',
            array('associated_attributes' => array('General' => $attributes)));
        $simpleWithAttributes = $this->loadDataSet('Product', 'simple_product_visible',
            array('categories'                    => $anchorCategoryPath . '/' . $anchorSubCategory['name'],
                  'product_attribute_set'         => $attributeSet['set_name']));
        $simpleWithAttributes['general_user_attr']['dropdown'][$dropdown['attribute_code']] =
            $dropdown['option_1']['admin_option_name'];
        $simpleWithAttributes['general_user_attr']['field'][$price['attribute_code']] = '999';
        $simpleWithAttributes['general_user_attr']['multiselect'][$multiselect['attribute_code']] =
            $multiselect['option_2']['admin_option_name'];
        $simpleWithoutAttributes =
            $this->loadDataSet('Product', 'simple_product_visible', array('categories' => $anchorCategoryPath));
        //Steps
        $this->loginAdminUser();
        //Creating categories
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        foreach (array($anchorCategory, $anchorSubCategory, $nonAnchorCategory, $nonAnchorSubCategory) as $category) {
            $this->categoryHelper()->createCategory($category);
            $this->assertMessagePresent('success', 'success_saved_category');
        }
        //Creating attributes
        $this->navigate('manage_attributes');
        foreach (array($dropdown, $multiselect, $price) as $attribute) {
            $this->productAttributeHelper()->createAttribute($attribute);
            $this->assertMessagePresent('success', 'success_saved_attribute');
        }
        // Creating attribute set
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        //Creating products
        $this->navigate('manage_products');
        foreach (array($simpleWithAttributes, $simpleWithoutAttributes) as $product) {
            $this->productHelper()->createProduct($product);
            $this->assertMessagePresent('success', 'success_saved_product');
        }

        return array('simpleAnchor'          => $simpleWithAttributes['general_name'],
                     'simpleNonAnchor'       => $simpleWithoutAttributes['general_name'],
                     'multiselectOptionName' => $multiselect['option_2']['store_view_titles']['Default Store View'],
                     'dropdownOptionName'    => $dropdown['option_1']['store_view_titles']['Default Store View'],
                     'multiselectCode'       => $multiselect['attribute_code'],
                     'dropdownCode'          => $dropdown['attribute_code'],
                     'priceCode'             => $price['attribute_code'],
                     'anchorCategory'        => $anchorCategory['name'],
                     'anchorSubCategory'     => $anchorSubCategory['name'],
                     'nonAnchorCategory'     => $nonAnchorCategory['name']);
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
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5610
     */
    public function checkLayeredNavigationOnNonAnchorCategoryPage($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['nonAnchorCategory']);
        //Verifying
        $this->assertTrue($this->controlIsPresent('fieldset', 'layered_navigation'),
            'There is no LN block on the' . $data['nonAnchorCategory'] . 'non-anchor category page');
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
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5606
     */
    public function checkLayeredNavigationOnAnchorCategoryPage($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        //Verifying
        $this->assertTrue($this->controlIsPresent('fieldset', 'layered_navigation_anchor'),
            'There is no LN block on the ' . $data['anchorCategory'] . 'anchor category');
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
     *
     * @test
     * @depends preconditionsForTests
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @TestlinkId TL-MAGE-5607
     */
    public function selectCategoryAnchor($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()->setCategoryIdFromLink($data['anchorSubCategory']);
        $this->clickControl('link', 'category_name');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_name_header'),
            'Product assigned to category page displays after filtering');
    }

    /**
     * <p>Removing selected category from the anchor category layered navigation block using Remove button</p>
     * <p>Steps</p>
     * <p>1. Click on remove_this_item button </p>
     * <p>Expected Result:</p>
     * <p>Subcategory removed from currently_shopping_by block</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @depends selectCategoryAnchor
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @TestlinkId TL-MAGE-5608
     */
    public function removeSelectedCategoryAnchor($data)
    {
        //Steps
        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Removing selected category from the anchor category layered navigation block using Clear All link</p>
     * <p>Steps</p>
     * <p>1. Click on subcategory in layered navigation block </p>
     * <p>2. Click on Clear All link </p>
     * <p>Expected Result:</p>
     * <p>Subcategory removed from currently_shopping_by block</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @depends selectCategoryAnchor
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @TestlinkId TL-MAGE-5609
     */
    public function removeSelectedCategoryAnchorClearAll($data)
    {
        //Steps
        $this->layeredNavigationHelper()->setCategoryIdFromLink($data['anchorSubCategory']);
        $this->clickControl('link', 'category_name');
        $this->assertTrue($this->controlIsPresent('pageelement', 'currently_shopping_by'),
            'There is no currently_shopping_by block in layered navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
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
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5649
     */
    public function selectDropdownAttribute($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()
            ->setAttributeIdFromLink($data['anchorCategory'], $data['dropdownCode'], $data['dropdownOptionName']);
        $this->clickControl('link', 'attribute_name');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_name_header'),
            'Product assigned to category page displays after filtering');
    }

    /**
     * <p>Removing selected dropdown attribute using Remove button</p>
     * <p>Steps</p>
     * <p>1. Click on remove_this_item button </p>
     * <p>Expected Result:</p>
     * <p>Dropdown attribute removed from currently_shopping_by block</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @depends selectDropdownAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @TestlinkId TL-MAGE-5650
     */
    public function removeSelectedDropdown($data)
    {
        //Steps
        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Removing selected dropdown attribute using Clear All link</p>
     * <p>Steps</p>
     * <p>1. Click on dropdown attribute in layered navigation block </p>
     * <p>2. Click on Clear All link </p>
     * <p>Expected Result:</p>
     * <p>Dropdown attribute removed from currently_shopping_by block</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @depends selectDropdownAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @TestlinkId TL-MAGE-5651
     */
    public function removeDropdownAttributeClearAll($data)
    {
        //Steps
        $this->layeredNavigationHelper()
            ->setAttributeIdFromLink($data['anchorCategory'], $data['dropdownCode'], $data['dropdownOptionName']);
        $this->clickControl('link', 'attribute_name');
        $this->assertTrue($this->controlIsPresent('pageelement', 'currently_shopping_by'),
            'There is no currently_shopping_by block in layered navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
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
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5649
     */
    public function selectMultiselectAttribute($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()
            ->setAttributeIdFromLink($data['anchorCategory'], $data['multiselectCode'], $data['multiselectOptionName']);
        $this->clickControl('link', 'attribute_name');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_name_header'),
            'Product assigned to category page displays after filtering');
    }

    /**
     * <p>Removing selected multiselect attribute using Remove button</p>
     * <p>Steps</p>
     * <p>1. Click on remove_this_item button </p>
     * <p>Expected Result:</p>
     * <p>multiselect attribute removed from currently_shopping_by block</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @depends selectMultiselectAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @TestlinkId TL-MAGE-5650
     */
    public function removeSelectedMultiselect($data)
    {
        //Steps
        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Removing selected dropdown attribute using Clear All link</p>
     * <p>Steps</p>
     * <p>1. Click on dropdown attribute in layered navigation block </p>
     * <p>2. Click on Clear All link </p>
     * <p>Expected Result:</p>
     * <p>Dropdown attribute removed from currently_shopping_by block</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @depends selectMultiselectAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @TestlinkId TL-MAGE-5651
     */
    public function removeMultiselectAttributeClearAll($data)
    {
        //Steps
        $this->layeredNavigationHelper()
            ->setAttributeIdFromLink($data['anchorCategory'], $data['multiselectCode'], $data['multiselectOptionName']);
        $this->clickControl('link', 'attribute_name');
        $this->assertTrue($this->controlIsPresent('pageelement', 'currently_shopping_by'),
            'There is no currently_shopping_by block in layered navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
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
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5649
     */
    public function selectPriceAttribute($data)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()->setAttributeIdFromLink($data['anchorCategory'], $data['multiselectCode']);
        $this->clickControl('link', 'price_attribute');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_name_header'),
            'Product assigned to category page displays after filtering');
    }

    /**
     * <p>Removing selected price attribute using Remove button</p>
     * <p>Steps</p>
     * <p>1. Click on remove_this_item button </p>
     * <p>Expected Result:</p>
     * <p>price attribute removed from currently_shopping_by block</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @depends selectPriceAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @TestlinkId TL-MAGE-5650
     */
    public function removeSelectedPriceAttribute($data)
    {
        //Steps
        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Removing selected price attribute using Clear All link</p>
     * <p>Steps</p>
     * <p>1. Click on dropdown attribute in layered navigation block </p>
     * <p>2. Click on Clear All link </p>
     * <p>Expected Result:</p>
     * <p>Price attribute removed from currently_shopping_by block</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @depends selectPriceAttribute
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @TestlinkId TL-MAGE-5651
     */
    public function removePriceAttributeClearAll($data)
    {
        //Steps
        $this->layeredNavigationHelper()->setAttributeIdFromLink($data['anchorCategory'], $data['multiselectCode']);
        $this->clickControl('link', 'price_attribute');
        $this->assertTrue($this->controlIsPresent('pageelement', 'currently_shopping_by'),
            'There is no currently_shopping_by block in layered navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name_header'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }
}