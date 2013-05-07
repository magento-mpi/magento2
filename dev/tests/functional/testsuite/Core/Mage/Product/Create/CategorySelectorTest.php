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
class Core_Mage_Product_Create_CategorySelectorTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * Create categories
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $default = $this->loadDataSet('Category', 'sub_category_required');
        $additional = $this->loadDataSet('Category', 'sub_category_required');
        $newRoot = $this->loadDataSet('Category', 'root_category_required');
        $subInNew = $this->loadDataSet('Category', 'sub_category_required',
            array(
                 'parent_category' => $newRoot['name'],
                 'name' => $default['name']
            ));
        //Create root category
        $this->navigate('manage_categories');
        $this->categoryHelper()->createCategory($newRoot);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Create new categories in 'Default Category'
        $this->categoryHelper()->createCategory($default);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        $this->categoryHelper()->createCategory($default);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Create new category in created root category
        $this->categoryHelper()->createCategory($subInNew);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Create additional category in 'Default Category'
        $this->categoryHelper()->createCategory($additional);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');

        return array('default'    => $default['parent_category'] . '/' . $default['name'],
                     'newRoot'    => $subInNew['parent_category'] . '/' . $subInNew['name'],
                     'additional' => $additional['parent_category'] . '/' . $additional['name']);
    }

    /**
     * @param string $categoryName
     *
     * @test
     * @dataProvider categoryNameDataProvider
     * @TestlinkId TL-MAGE-6348
     */
    public function selectCategory($categoryName)
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required', array('name' => $categoryName));
        $afterSave = $this->_getExpectedCategoryNameAfterSave($category['name']);
        $product = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $category['parent_category'] . '/' . $afterSave));
        //Preconditions
        $this->navigate('manage_categories');
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $product['general_sku']));
        $this->productHelper()->verifyProductInfo($product);
    }

    /**
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6357
     */
    public function selectCategoryWithSubcategories($categories)
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category' => $categories['newRoot']));
        $productData = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $categories['newRoot']));
        //Preconditions
        $this->navigate('manage_categories');
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6356
     */
    public function selectSameCategoryTwice($categories)
    {
        //Data
        $product = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $categories['additional']));
        $explodeCategory = explode('/', $product['general_categories']);
        $categoryName = end($explodeCategory);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product, 'simple', false);
        $this->productHelper()->openProductTab('general');
        $this->getControlElement(self::FIELD_TYPE_INPUT, 'general_categories')->value($categoryName);
        $this->waitUntil(function ($testCase) {
                /** @var Mage_Selenium_TestCase $testCase */
                $class = $testCase->getControlAttribute('field', 'general_categories', 'class');
                if (strpos($class, 'mage-suggest-state-loading') === false) {
                    return true;
                }
            }, 40000
        );
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'categories_list');
        //Verifying
        $this->assertTrue($this->controlIsVisible(self::FIELD_TYPE_PAGEELEMENT, 'no_category_found'));
    }

    /**
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6350
     */
    public function selectTwoDifferentCategories($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => array($categories['default'], $categories['additional'])));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6351
     */
    public function selectTwoCategoriesWithSameNameInOneRootCategory($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => array($categories['default'], $categories['default'])));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6349
     */
    public function selectTwoCategoriesWithSameNameInDifferentRootCategories($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => array($categories['default'], $categories['newRoot'])));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * @test
     * @depends selectCategory
     * @TestlinkId TL-MAGE-6353
     */
    public function searchForNonexistentCategory()
    {
        //Data
        $nonexistentCategory = $this->generate('string', 7, ':alnum:');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->selectTypeProduct('simple');
        $this->getControlElement(self::FIELD_TYPE_INPUT, 'general_categories')->value($nonexistentCategory);
        $this->waitForControlEditable(self::FIELD_TYPE_INPUT, 'general_categories');
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'categories_list');
        //Verifying
        $this->assertTrue($this->controlIsVisible(self::FIELD_TYPE_PAGEELEMENT, 'no_category_found'),
            "Category $nonexistentCategory was founded");
    }

    /**
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6352
     */
    public function deleteSelectedCategory($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $categories['default']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->openProductTab('general');
        $this->clickControl('link', 'chosen_category_delete', false);
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->assertEquals('', $this->getControlAttribute('field', 'general_categories', 'value'),
            'Category was not unassigned from product.');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6448
     */
    public function createNewCategoryValidationFailed()
    {
        $this->markTestIncomplete('MAGETWO-8857');
        $this->navigate('manage_products');
        $this->productHelper()->selectTypeProduct('simple');

        $this->clickButton('new_category', false);
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'new_category_form');
        // no validation messages displayed
        $this->assertFalse($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'category_name_required'),
            '"This is a required field" message appeared for Category Name'
        );
        $this->assertFalse($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'parent_name_required'),
            '"This is a required field" message appeared for Parent Category'
        );
        $this->assertFalse($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'parent_name_existent'),
            '"Choose existing category" message appeared'
        );

        $this->clickButton('new_category_save', false);
        // required fields validation messages shown after save attempt without data entering
        $this->assertTrue($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'category_name_required'),
            '"This is a required field" message is not appear for Category Name'
        );
        $this->assertTrue($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_required'),
            '"This is a required field" message is not appear for Parent Category'
        );
        $this->assertFalse($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'parent_name_existent'),
            '"Choose existing category" message appeared'
        );

        $this->fillField('name', $this->generate('string', 256, ':alnum:'));
        $this->clickControl(self::FIELD_TYPE_INPUT, 'parent_category');
        $this->waitForControlEditable(self::FIELD_TYPE_INPUT, 'parent_category');
        $this->fillField('parent_category', $this->generate('string', 256, ':alnum:'));
        $this->clickButton('new_category_save', false);
        sleep(1); // giving time for messages to disappear with animation, waitForElementNotVisible would do the job

        // only "Choose existing category" validation message is displayed
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'category_name_required'),
            '"This is a required field" message appeared for Category Name'
        );
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_required'),
            '"This is a required field" message appeared for Parent Category');
        $this->assertTrue($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_existent'),
            '"Choose existing category" message is not appear'
        );

        $this->clickButton('new_category_cancel', false);
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_existent'),
            '"Choose existing category" message appeared'
        );

        $this->clickButton('new_category', false);
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'new_category_form');
        // fields are cleared, no validation messages displayed
        $this->assertEmpty($this->getControlAttribute(self::FIELD_TYPE_INPUT, 'name', 'selectedValue'),
            'Category Name field is not empty'
        );
        $this->assertEmpty($this->getControlAttribute(self::FIELD_TYPE_INPUT, 'parent_category', 'selectedValue'),
            'Parent Name field is not empty'
        );
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'category_name_required'),
            '"This is a required field" message appeared for Category Name'
        );
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_required'),
            '"This is a required field" message appeared for Parent Category'
        );
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_existent'),
            '"Choose existing category" message appeared'
        );
    }

    /**
     * @param string $newCategoryName
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @dataProvider categoryNameDataProvider
     * @TestlinkId TL-MAGE-6447
     */
    public function createNewCategorySuccessfully($newCategoryName, $categories)
    {
        //Data
        $explodePath = explode('/', $categories['default']);
        $path = array_shift($explodePath);
        $expectedNameAfterSave = $this->_getExpectedCategoryNameAfterSave($newCategoryName);
        $product = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $path . '/' . $newCategoryName)
        );
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product, 'simple');
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $product['general_sku']));
        $product['general_categories'] = $path . '/' . $expectedNameAfterSave;
        $this->productHelper()->verifyProductInfo($product);
        $this->navigate('manage_categories');
        $this->categoryHelper()->selectCategory($product['general_categories']);
        $this->openTab('general_information');
        $this->assertEquals($expectedNameAfterSave,
            $this->getControlAttribute(self::FIELD_TYPE_INPUT, 'name', 'value')
        );
        $this->assertEquals('Yes', $this->getControlAttribute(self::FIELD_TYPE_DROPDOWN, 'is_active', 'selectedLabel'));
    }

    /**
     * @todo data provider should be static
     * @return array
     */
    public function categoryNameDataProvider()
    {
        return array(
            array(str_replace(array('/', ',', '"'), '?',
                $this->generate('string', rand(20, 255), ':alnum:,:punct:'))),
            array(str_replace(array('/', ',', '"'), '?',
                $this->generate('string', rand(256, 512), ':alnum:,:punct:'))),
            array('<img src=example.com?nonexistent.jpg onerror=alert(' . $this->generate('string', 5) . ')>')
        );
    }

    /**
     * Currently category name is truncated after 255 characters
     *
     * @param string $categoryNameForSave
     *
     * @return string
     */
    protected function _getExpectedCategoryNameAfterSave($categoryNameForSave)
    {
        return substr($categoryNameForSave, 0, 255);
    }
}
