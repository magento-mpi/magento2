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
        $categoryDefault = $this->loadDataSet('Category', 'sub_category_required');
        $additionalCategory = $this->loadDataSet('Category', 'sub_category_required');
        $rootCategoryData = $this->loadDataSet('Category', 'root_category_required');
        $categoryNewRoot = $this->loadDataSet('Category', 'sub_category_required', array(
            'parent_category' => $rootCategoryData['name']
        ));
        //Create root category
        $this->navigate('manage_categories');
        $this->categoryHelper()->createCategory($rootCategoryData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Create new categories in 'Default Category'
        $this->categoryHelper()->createCategory($categoryDefault);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        $this->categoryHelper()->createCategory($categoryDefault);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Create new category in created root category
        $this->categoryHelper()->createCategory($categoryNewRoot);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Create additional category in 'Default Category'
        $this->categoryHelper()->createCategory($additionalCategory);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');

        return array(
            'default' => array('parent' => $categoryDefault['parent_category'], 'category' => $categoryDefault['name']),
            'newRoot' => array('parent' => $categoryNewRoot['parent_category'], 'category' => $categoryNewRoot['name']),
            'additionalDefault' => array(
                'parent' => $additionalCategory['parent_category'],
                'category' => $additionalCategory['name']
            )
        );
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
        $categoryData = $this->loadDataSet('Category', 'sub_category_required', array('name' => $categoryName));
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Preconditions
        $this->navigate('manage_categories');
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        $productData['categories'] = $categoryData['parent_category']
            . '/' . $this->_getExpectedCategoryNameAfterSave($categoryData['name']);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
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
        $categoryData = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category'=> $categories['newRoot']['parent'] . '/' . $categories['newRoot']['category']));
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['newRoot']['parent'] . '/' . $categories['newRoot']['category'];
        //Preconditions
        $this->navigate('manage_categories');
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_category');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
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
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6350
     */
    public function selectTwoDifferentCategories($categories)
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
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6351
     */
    public function selectTwoCategoriesWithSameNameInOneRootCategory($categories)
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
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6349
     */
    public function selectTwoCategoriesWithSameNameInDifferentRootCategories($categories)
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
     * @test
     * @depends selectCategory
     * @TestlinkId TL-MAGE-6353
     */
    public function searchForNonexistentCategory()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $selectedCategory = $this->generate('string', 20, ':alnum:');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->selectTypeProduct($productData, 'simple');
        $this->fillField('categories', $selectedCategory);
        $this->keyDown($this->_getControlXpath('field', 'categories'), ' ');
        $this->waitForAjax();
        //Verifying
        $this->assertFalse($this->controlIsVisible('fieldset', 'category_search'), 'Category list is not empty.');
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
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['default']['parent'] . '/' . $categories['default']['category'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->openTab('general');
        $this->clickControl('link', 'delete_category', false);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->assertEquals('', $this->getControlAttribute('field', 'categories', 'value'),
            'Category was not unassigned from product.');
    }

    /**
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6354
     * @todo move these checks to "duplicate product" test case
     */
    public function duplicateProduct($categories)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['categories'] = $categories['default']['parent'] . '/' . $categories['default']['category'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->clickButton('duplicate');
        //Verifying
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_duplicated_product');
        $productData['general_sku'] = $this->productHelper()->getGeneratedSku($productData['general_sku']);
        $this->productHelper()->verifyProductInfo($productData, array('general_status'));
    }

    /**
     * @param array $categories
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6355
     * @todo move these checks to "change attribute set" test case
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
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_attribute_set_saved');
        $productData['product_attribute_set'] = $attributeSet['set_name'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6448
     */
    public function createNewCategoryValidationFailed()
    {
        $this->navigate('manage_products');
        $this->productHelper()->createProduct(array(), 'simple', false);
        $this->openTab('general');

        // manual check of message by xpath is needed because assertMessageNotPresent fails on invisible messages
        $newCategoryForm = $this->_getControlXpath(self::UIMAP_TYPE_FIELDSET, 'new_category_form');

        $this->clickButton('new_category', false);
        $this->waitForElementVisible($newCategoryForm);
        // no validation messages displayed
        $this->assertFalse($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'category_name_required'));
        $this->assertFalse($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'parent_name_required'));
        $this->assertFalse($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'parent_name_existent'));

        $this->clickButton('new_category_save', false);
        // required fields validation messages shown after save attempt without data entering
        $this->assertTrue($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'category_name_required'));
        $this->assertTrue($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_required'));
        $this->assertFalse($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'parent_name_existent'));

        $this->fillFieldset(array(
            'name' => $this->generate('string', 256, ':alnum:'),
            'parent_category' => $this->generate('string', 256, ':alnum:'),
        ), 'new_category_form');
        $this->clickButton('new_category_save', false);
        sleep(1); // giving time for messages to disappear with animation, waitForElementNotVisible would do the job

        // only "Choose existing category" validation message is displayed
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'category_name_required'));
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_required'));
        $this->assertTrue($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_existent'));

        $this->clickButton('new_category_cancel', false);
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_existent'));

        $this->clickButton('new_category', false);
        $this->waitForElementVisible($newCategoryForm);
        // fields are cleared, no validation messages displayed
        $this->assertEmpty($this->getControlAttribute(self::FIELD_TYPE_INPUT, 'name', 'value'));
        $this->assertEmpty($this->getControlAttribute(self::FIELD_TYPE_INPUT, 'parent_category', 'value'));
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'category_name_required'));
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_required'));
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_MESSAGE, 'parent_name_existent'));
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
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $parentCategory = $categories['default']['category'];
        $parentCategoryPath = $categories['default']['parent'] . '/' . $parentCategory;
        $expectedCategoryNameAfterSave = $this->_getExpectedCategoryNameAfterSave($newCategoryName);
        $newCategoryNameBeginning = substr($newCategoryName, 0, rand(5, strlen($newCategoryName) - 5));

        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->openTab('general');
        $this->fillField('categories', $newCategoryNameBeginning);

        $this->clickButton('new_category', false);
        $this->waitForElementVisible($this->_getControlXpath(self::UIMAP_TYPE_FIELDSET, 'new_category_form'));
        // check new category name pre-population
        $this->assertEquals($newCategoryNameBeginning,
            $this->getControlAttribute(self::FIELD_TYPE_INPUT, 'name', 'value'));

        $this->fillField('name', $newCategoryName);
        $this->_chooseParentCategory($parentCategory);

        $this->clickButton('new_category_save', false);
        // wait for new category to appear in selected categories list
        $this->addParameter('categoryName', $expectedCategoryNameAfterSave);
        $this->waitForElementVisible($this->_getControlXpath(self::FIELD_TYPE_PAGEELEMENT, 'category_name'));
        $this->assertFalse($this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'new_category_form'));
        // save the product and verify saved data
        $this->clickButton('save', true);
        $this->assertMessagePresent(self::MESSAGE_TYPE_SUCCESS, 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData);
        // check that category is saved with correct name and is active
        $newCategoryPath = $parentCategoryPath . '/' . $expectedCategoryNameAfterSave;
        $this->navigate('manage_categories');
        $this->categoryHelper()->selectCategory($newCategoryPath);
        $this->openTab('general_information');
        $this->assertEquals($expectedCategoryNameAfterSave,
            $this->getControlAttribute(self::FIELD_TYPE_INPUT, 'name', 'value'));
        $this->assertEquals('Yes', $this->getControlAttribute(self::FIELD_TYPE_DROPDOWN, 'is_active', 'selectedLabel'));
    }

    /**
     * @todo data provider should be static
     * @return array
     */
    public function categoryNameDataProvider()
    {
        return array(
            array(str_replace(array('\\', '/', ',', '"'), '?',
                $this->generate('string', rand(20, 255), ':alnum:,:punct:'))
            ),
            array(str_replace(array('\\', '/', ',', '"'), '?',
                $this->generate('string', rand(256, 512), ':alnum:,:punct:'))
            ),
            array('<img src=example.com?nonexistent.jpg onerror=alert("xss")>'),
        );
    }

    /**
     * Choose parent category from suggestions list
     *
     * @param string $parentCategory
     */
    protected function _chooseParentCategory($parentCategory)
    {
        $this->fillField('parent_category', $parentCategory);
        $this->typeKeys($this->_getControlXpath(self::FIELD_TYPE_INPUT, 'parent_category'), "\b");
        $this->addParameter('categoryName', $parentCategory);
        $parentCategoryInDropdown = $this->_getControlXpath(self::FIELD_TYPE_LINK, 'suggested_category_name');
        $this->waitForElementVisible($parentCategoryInDropdown);
        $this->mouseOver($parentCategoryInDropdown);
        $this->clickControl(self::FIELD_TYPE_LINK, 'suggested_category_name', false);
    }

    /**
     * Currently category name is truncated after 255 characters
     *
     * @param string $categoryNameForSave
     * @return string
     */
    protected function _getExpectedCategoryNameAfterSave($categoryNameForSave)
    {
        return substr($categoryNameForSave, 0, 255);
    }
}
