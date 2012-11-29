<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Category assignment on general tab
 */
class Core_Mage_Product_CategoryTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     *  <p>1. Log in to Backend.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions for tests</p>
     *  <p>1. Creating categories</p>
     *
     * @return array
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $categoryDefault = $this->loadDataSet('Category', 'sub_category_required');
        $additionalCategory = $this->loadDataSet('Category', 'sub_category_required');
        $rootCategoryData = $this->loadDataSet('Category', 'root_category_required');
        $categoryNewRoot = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category' => $rootCategoryData['name']));
        //Create root category
        $this->navigate('manage_categories');
        $this->categoryHelper()->createCategory($rootCategoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Create new categories in 'Default Category'
        $this->categoryHelper()->createCategory($categoryDefault);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->createCategory($categoryDefault);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Create new category in created root category
        $this->categoryHelper()->createCategory($categoryNewRoot);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Create additional category in 'Default Category'
        $this->categoryHelper()->createCategory($additionalCategory);
        $this->assertMessagePresent('success', 'success_saved_category');

        return array(
            'default' => array('parent' => $categoryDefault['parent_category'], 'category' => $categoryDefault['name']),
            'newRoot' => array('parent' => $categoryNewRoot['parent_category'], 'category' => $categoryNewRoot['name']),
            'additionalDefault' => array('parent' => $additionalCategory['parent_category'],
                'category' => $additionalCategory['name'])
        );
    }

    /**
     * <p>Select category and save product</p>
     *
     * @param string $categoryName
     *
     * @test
     * @dataProvider categoryNamesDataProvider
     * @TestlinkId TL-MAGE-6348
     */
    public function selectCategory($categoryName)
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_required', array('name' => $categoryName));
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Preconditions
        $this->navigate('manage_categories');
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $productData['categories'] = $categoryData['parent_category'] . '/' . $categoryData['name'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>DataProvider with category name list</p>
     *
     * @return array
     */
    public function categoryNamesDataProvider()
    {
        return array(
            array($this->generate('string', 20, ':alnum:')),
            array($this->generate('string', 255, ':alnum:')),
            array(str_replace(array('/', ',', '"'), '?', $this->generate('string', 20, ':punct:'))),
            array('<img src=nonexistentwebsite.com?nonexistent.jpg onerror=alert("xss")>'),
        );
    }

    /**
     * <p>Select category with subcategory</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6357
     */
    public function selectCategoryWithSubcategories($categories)
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category'=> $categories['newRoot']['parent'] . '/' . $categories['newRoot']['category']));
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['newRoot']['parent'] . '/' . $categories['newRoot']['category'];
        //Preconditions
        $this->navigate('manage_categories');
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Select the same category two times.</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6356
     */
    public function selectOneCategoryTwoTimes($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['newRoot']['parent'] . '/' . $categories['newRoot']['category'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->openTab('general');
        $this->fillField('categories', $categories['newRoot']['category']);
        $this->waitForElementVisible($this->_getControlXpath('fieldset', 'category_search'));
        //Verifying
        $this->assertTrue($this->controlIsPresent('link', 'selected_category'),
            'Selected category is not highlighted.');
    }

    /**
     * <p>Select two different categories</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6350
     */
    public function selectTwoCategories($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['default']['parent'] . '/' . $categories['default']['category'] . ', '
            . $categories['additionalDefault']['parent'] . '/' . $categories['additionalDefault']['category'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Select two categories with the same name in one root categories</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6351
     */
    public function withSameNameInOneRootCategory($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['default']['parent'] . '/' . $categories['default']['category'] . ', '
            . $categories['default']['parent'] . '/' . $categories['default']['category'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Select two categories with the same name in different root categories</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6349
     */
    public function withSameNameInDifferentRootCategory($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['default']['parent'] . '/' . $categories['default']['category'] . ', '
            . $categories['newRoot']['parent'] . '/' . $categories['newRoot']['category'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Search for nonexistent category</p>
     *
     * @test
     * @depends selectCategory
     * @TestlinkId TL-MAGE-6353
     */
    public function searchNonexistentCategory()
    {
        //Data
        $selectedCategory = $this->generate('string', 20, ':alnum:');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->selectTypeProduct('simple');
        $this->fillField('categories', $selectedCategory);
        $this->waitForAjax();
        //Verifying
        $this->assertFalse($this->controlIsVisible('fieldset', 'category_search'), 'Category list is not empty.');
    }

    /**
     * <p>Delete selected category</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Selected category was successfully unassigned.</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6352
     */
    public function deleteSelectedCategory($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['default']['parent'] . '/' . $categories['default']['category'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->openTab('general');
        $this->clickControl('link', 'delete_category', false);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->assertEquals('', $this->getControlAttribute('field', 'categories', 'value'),
            'Category was not unassigned from product.');
    }

    /**
     * <p>Duplicate product</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6354
     */
    public function duplicateProduct($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['default']['parent'] . '/' . $categories['default']['category'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->clickButton('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $productData['general_sku'] = $this->productHelper()->getGeneratedSku($productData['general_sku']);
        $this->productHelper()->verifyProductInfo($productData, array('general_status'));
    }

    /**
     * <p>Change attribute set</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6355
     */
    public function changeAttributeSet($categories)
    {
        //Data
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['default']['parent'] . '/' . $categories['default']['category'];
        $newAttributeSet = 'Default';
        //Preconditions
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $productData['product_attribute_set'] = $attributeSet['set_name'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }
}