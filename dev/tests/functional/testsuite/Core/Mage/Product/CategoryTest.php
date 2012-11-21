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
     * <p>Preconditions:</p>
     *  <p>1. Category has been created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Navigate to Catalog - Manage Products.</p>
     *  <p>3. Click 'Add Product' button.</p>
     *  <p>4. Fulfill all required fields.</p>
     *  <p>5. Enter created category name into the category control field and choose proper category from list.</p>
     *  <p>6. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product successfully saved with selected category.</p>
     *  <p>2. System displays message 'The product has been saved.'</p>
     *
     * @param string $categoryName
     *
     * @test
     * @dataProvider categoryNamesDataProvider
     * @TestlinkId TL-MAGE-6348
     * @author Dmytro_Aponasenko
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
     * <p>Preconditions:</p>
     *  <p>1. Category with subcategory has been created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Navigate to Catalog - Manage Products.</p>
     *  <p>3. Click 'Add Product' button.</p>
     *  <p>4. Fulfill all required fields.</p>
     *  <p>5. Select category name which contains subcategory in the category control.</p>
     *  <p>6. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product successfully saved with selected category.</p>
     *  <p>2. System displays message 'The product has been saved.'</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6357
     * @author Dmytro_Aponasenko
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
     * <p>Preconditions:</p>
     *  <p>1. Category has been created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Navigate to Catalog - Manage Products.</p>
     *  <p>3. Click 'Add Product' button.</p>
     *  <p>4. Fulfill all required fields.</p>
     *  <p>5. Enter created category name into the category control field and choose proper category from list.</p>
     *  <p>6. Enter the same category name into the category control field.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Selected category is not displayed in the list with proposed categories.</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6356
     * @author Dmytro_Aponasenko
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
        $this->keyDown($this->_getControlXpath('field', 'categories'), ' ');
        $this->waitForElementVisible($this->_getControlXpath('fieldset', 'category_search'));
        //Verifying
        $this->assertTrue($this->controlIsPresent('link', 'selected_category'),
            'Selected category is not highlighted.');
    }

    /**
     * <p>Select two different categories</p>
     * <p>Preconditions:</p>
     *  <p>1. Two categories have been created in default root category.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Navigate to Catalog - Manage Products.</p>
     *  <p>3. Click 'Add Product' button.</p>
     *  <p>4. Fulfill all required fields.</p>
     *  <p>5. Enter first category name into the category control field and choose proper category from list.</p>
     *  <p>6. Enter second category name into the category control field and choose proper category from list.</p>
     *  <p>7. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product successfully saved with selected categories.</p>
     *  <p>2. System displays message 'The product has been saved.'</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6350
     * @author Dmytro_Aponasenko
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
     * <p>Preconditions:</p>
     *  <p>1. Two categories with the same name have been created in default root category.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Navigate to Catalog - Manage Products.</p>
     *  <p>3. Click 'Add Product' button.</p>
     *  <p>4. Fulfill all required fields.</p>
     *  <p>5. Enter category name into the category control field and choose proper category from list.</p>
     *  <p>6. Enter category name into the category control field and choose proper category from list.</p>
     *  <p>7. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product successfully saved with selected categories.</p>
     *  <p>2. System displays message 'The product has been saved.'</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6351
     * @author Dmytro_Aponasenko
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
     * <p>Preconditions:</p>
     *  <p>1. New root category has been created.</p>
     *  <p>2. Category 'Category_test' has been created in new root category.</p>
     *  <p>3. Category 'Category_test' has been created in root 'Default Category'.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Navigate to Catalog - Manage Products.</p>
     *  <p>3. Click 'Add Product' button.</p>
     *  <p>4. Fulfill all required fields.</p>
     *  <p>5. Enter 'Category_test' into the category control field and choose category in 'Default Category'.</p>
     *  <p>6. Enter 'Category_test' into the category control field and choose category placed in new root category.</p>
     *  <p>7. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product successfully saved with selected categories.</p>
     *  <p>2. System displays message 'The product has been saved.'</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6349
     * @author Dmytro_Aponasenko
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
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Navigate to Catalog - Manage Products.</p>
     *  <p>3. Click 'Add Product' button.</p>
     *  <p>4. Fulfill all required fields.</p>
     *  <p>5. Enter nonexistent category name into the category control field.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. List with proposed categories is empty.</p>
     *
     * @test
     * @depends selectCategory
     * @TestlinkId TL-MAGE-6353
     * @author Dmytro_Aponasenko
     */
    public function searchNonexistentCategory()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $selectedCategory = $this->generate('string', 20, ':alnum:');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->selectTypeProduct('simple');
        $this->fillField('categories', $selectedCategory);
        $this->keyDown($this->_getControlXpath('field', 'categories'), ' ');
        $this->waitForAjax();
        //Verifying
        $this->assertFalse($this->controlIsVisible('fieldset', 'category_search'), 'Category list is not empty.');
    }

    /**
     * <p>Delete selected category</p>
     * <p>Preconditions:</p>
     *  <p>1. Category has been created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Navigate to Catalog - Manage Products.</p>
     *  <p>3. Click 'Add Product' button.</p>
     *  <p>4. Fulfill all required fields.</p>
     *  <p>5. Enter category name into the category control field and choose proper category from list.</p>
     *  <p>6. Delete selected category.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Selected category was successfully unassigned.</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6352
     * @author Dmytro_Aponasenko
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
        $this->assertEquals('', $this->getValue($this->_getControlXpath('field', 'categories')),
            'Category was not unassigned from product.');
    }

    /**
     * <p>Duplicate product</p>
     * <p>Preconditions:</p>
     *  <p>1. Category has been created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Navigate to Catalog - Manage Products.</p>
     *  <p>3. Click 'Add Product' button.</p>
     *  <p>4. Fulfill all required fields.</p>
     *  <p>5. Enter category name into the category control field and choose proper category from list.</p>
     *  <p>6. Save product.</p>
     *  <p>7. Open created product.</p>
     *  <p>8. Click 'Duplicate' button.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product was successfully duplicated.</p>
     *  <p>2. System displays message 'The product has been duplicated.'.</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6354
     * @author Dmytro_Aponasenko
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
     * <p>Preconditions:</p>
     *  <p>1. Category has been created.</p>
     *  <p>2. Attribute set based on default has been created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Navigate to Catalog - Manage Products.</p>
     *  <p>3. Click 'Add Product' button.</p>
     *  <p>4. Fulfill all required fields.</p>
     *  <p>5. Enter category name into the category control field and choose proper category from list.</p>
     *  <p>6. Change attribute set.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Attribute set was successfully changed.</p>
     *  <p>2. Selected category is displayed.</p>
     *
     * @param string $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6355
     * @author Dmytro_Aponasenko
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
