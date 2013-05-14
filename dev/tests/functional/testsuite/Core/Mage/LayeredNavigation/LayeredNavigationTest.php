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
class Core_Mage_LayeredNavigation_LayeredNavigationTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->frontend();
    }

    /**
     * <p>Creating categories and products</p>
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
        $anchorSubCategory = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category' => $anchorCategoryPath));
        $nonAnchorCategory = $this->loadDataSet('Category', 'sub_category_required');
        $nonAnchorCategPath = $nonAnchorCategory['parent_category'] . '/' . $nonAnchorCategory['name'];
        $nonAnchorSubCategory = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category' => $nonAnchorCategPath));
        $dropDown = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $multiSelect = $this->loadDataSet('ProductAttribute', 'product_attribute_multiselect_with_options');
        $price = $this->loadDataSet('ProductAttribute', 'product_attribute_price');
        $attributes = array($dropDown['attribute_code'], $multiSelect['attribute_code'], $price['attribute_code']);
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set',
            array('associated_attributes' => array('Product Details' => $attributes)));
        $simpleWithAttributes = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $anchorCategoryPath . '/' . $anchorSubCategory['name'],
                'product_attribute_set' => $attributeSet['set_name']));
        $simpleWithAttributes['general_user_attr']['dropdown'][$dropDown['attribute_code']] =
            $dropDown['option_1']['admin_option_name'];
        $simpleWithAttributes['general_user_attr']['field'][$price['attribute_code']] = '999';
        $simpleWithAttributes['general_user_attr']['multiselect'][$multiSelect['attribute_code']] =
            $multiSelect['option_2']['admin_option_name'];
        $simpleWithoutAttrs = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $anchorCategoryPath));
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
        foreach (array($dropDown, $multiSelect, $price) as $attribute) {
            $this->productAttributeHelper()->createAttribute($attribute);
            $this->assertMessagePresent('success', 'success_saved_attribute');
        }
        // Creating attribute set
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        //Creating products
        $this->navigate('manage_products');
        foreach (array($simpleWithAttributes, $simpleWithoutAttrs) as $product) {
            $this->productHelper()->createProduct($product);
            $this->assertMessagePresent('success', 'success_saved_product');
        }

        return array('simpleAnchor'          => $simpleWithAttributes['general_name'],
                     'simpleNonAnchor'       => $simpleWithoutAttrs['general_name'],
                     'multiselectOptionName' => $multiSelect['option_2']['store_view_titles']['Default Store View'],
                     'dropdownOptionName'    => $dropDown['option_1']['store_view_titles']['Default Store View'],
                     'multiselectCode'       => $multiSelect['attribute_code'],
                     'dropdownCode'          => $dropDown['attribute_code'],
                     'priceCode'             => $price['attribute_code'],
                     'anchorCategory'        => $anchorCategory['name'],
                     'anchorSubCategory'     => $anchorSubCategory['name'],
                     'nonAnchorCategory'     => $nonAnchorCategory['name']);
    }

    /**
     * <p>Checking that layered navigation block present on the non-anchor category page</p>
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
        $this->categoryHelper()->frontOpenCategory($data['nonAnchorCategory']);
        //Verifying
        $this->assertTrue($this->controlIsPresent('fieldset', 'layered_navigation'),
            'There is no LN block on the' . $data['nonAnchorCategory'] . 'non-anchor category page');
    }

    /**
     * <p>Checking that layered navigation block present on the anchor category page</p>
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
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        //Verifying
        $this->assertTrue($this->controlIsPresent('fieldset', 'layered_navigation_anchor'),
            'There is no LN block on the ' . $data['anchorCategory'] . 'anchor category');
    }

    /**
     * <p>Selecting/Removing subcategory in anchor category layered navigation block</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @depends checkLayeredNavigationOnAnchorCategoryPage
     * @TestlinkId TL-MAGE-5607,TL-MAGE-5608
     */
    public function selectCategoryAnchor($data)
    {
        //Steps
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()->setCategoryIdFromLink($data['anchorSubCategory']);
        $this->clickControl('link', 'category_name');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_name'),
            'Product assigned to category page displays after filtering');

        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Removing selected category from the anchor category layered navigation block using Clear All link</p>
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
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()->setCategoryIdFromLink($data['anchorSubCategory']);
        $this->clickControl('link', 'category_name');
        $this->assertTrue($this->controlIsPresent('pageelement', 'currently_shopping_by'),
            'There is no currently_shopping_by block in layered navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Selecting/Removing dropdown attribute</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5649,TL-MAGE-5650
     */
    public function selectDropdownAttribute($data)
    {
        //Steps
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()
            ->setAttributeIdFromLink($data['anchorCategory'], $data['dropdownCode'], $data['dropdownOptionName']);
        $this->clickControl('link', 'attribute_name');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_name'),
            'Product assigned to category page displays after filtering');

        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Removing selected dropdown attribute using Clear All link</p>
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
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()
            ->setAttributeIdFromLink($data['anchorCategory'], $data['dropdownCode'], $data['dropdownOptionName']);
        $this->clickControl('link', 'attribute_name');
        $this->assertTrue($this->controlIsPresent('pageelement', 'currently_shopping_by'),
            'There is no currently_shopping_by block in layered navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Selecting/Removing multiselect attribute</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5649,TL-MAGE-5650
     */
    public function selectMultiselectAttribute($data)
    {
        //Steps
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()
            ->setAttributeIdFromLink($data['anchorCategory'], $data['multiselectCode'], $data['multiselectOptionName']);
        $this->clickControl('link', 'attribute_name');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_name'),
            'Product assigned to category page displays after filtering');

        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Removing selected dropdown attribute using Clear All link</p>
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
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()
            ->setAttributeIdFromLink($data['anchorCategory'], $data['multiselectCode'], $data['multiselectOptionName']);
        $this->clickControl('link', 'attribute_name');
        $this->assertTrue($this->controlIsPresent('pageelement', 'currently_shopping_by'),
            'There is no currently_shopping_by block in layered navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Selecting/Removing price attribute</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5649,TL-MAGE-5650
     */
    public function selectPriceAttribute($data)
    {
        //Steps
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()->setAttributeIdFromLink($data['anchorCategory'], $data['multiselectCode']);
        $this->clickControl('link', 'price_attribute');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterSelectingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no product assigned to subcategory on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_name'),
            'Product assigned to category page displays after filtering');

        $this->clickControl('button', 'remove_this_item');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }

    /**
     * <p>Removing selected price attribute using Clear All link</p>
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
        $this->categoryHelper()->frontOpenCategory($data['anchorCategory']);
        $this->layeredNavigationHelper()->setAttributeIdFromLink($data['anchorCategory'], $data['multiselectCode']);
        $this->clickControl('link', 'price_attribute');
        $this->assertTrue($this->controlIsPresent('pageelement', 'currently_shopping_by'),
            'There is no currently_shopping_by block in layered navigation');
        $this->clickControl('link', 'clear_all');
        //Verifying
        $this->layeredNavigationHelper()->frontVerifyAfterRemovingAttribute();
        $this->addParameter('productName', $data['simpleAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no ' . $data['simpleAnchor'] . 'product on the page');
        $this->addParameter('productName', $data['simpleNonAnchor']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'There is no' . $data['simpleNonAnchor'] . 'product on the page');
    }
}