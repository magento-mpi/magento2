<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Category
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Category_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
    }

    /**
     * Deleting Root Category
     *
     * @test
     * @TestlinkId TL-MAGE-3167
     */
    public function deleteRootCategory()
    {
        //Data
        $rootCategoryData = $this->loadDataSet('Category', 'root_category_required');
        //Steps
        $this->categoryHelper()->createCategory($rootCategoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //Steps
        $this->categoryHelper()->selectCategory($rootCategoryData['name']);
        $this->categoryHelper()->deleteCategory('delete_category', 'confirm_delete');
        $this->assertMessagePresent('success', 'success_deleted_category');
    }

    /**
     * Deleting  Subcategory
     *
     * @test
     * @TestlinkId TL-MAGE-3170
     */
    public function deleteSubCategory()
    {
        //Data
        $subCategoryData = $this->loadDataSet('Category', 'sub_category_required');
        //Steps
        $this->categoryHelper()->createCategory($subCategoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //Steps
        $this->categoryHelper()->selectCategory($subCategoryData['parent_category'] . '/' . $subCategoryData['name']);
        $this->categoryHelper()->deleteCategory('delete_category', 'confirm_delete');
        $this->assertMessagePresent('success', 'success_deleted_category');
    }

    /**
     * Deleting Root Category that assigned to store
     *
     * @test
     * @TestlinkId TL-MAGE-3171
     */
    public function rootCategoryThatCannotBeDeleted()
    {
        //Data
        $rootCategoryData = $this->loadDataSet('Category', 'root_category_required');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $rootCategoryData['name']));
        //Steps
        $this->categoryHelper()->createCategory($rootCategoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //Steps
        $this->navigate('manage_stores');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('manage_stores'), $this->getParsedMessages());
        //Steps
        $this->storeHelper()->createStore($storeData, 'store');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->assertTrue($this->checkCurrentPage('manage_stores'), $this->getParsedMessages());
        //Steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->selectCategory($rootCategoryData['name']);
        //Verifying
        $this->assertFalse($this->buttonIsPresent('delete_category'), 'There is "Delete Category" button on the page');
    }

    /**
     * Deleting Root Category with Subcategory
     *
     * @test
     * @TestlinkId TL-MAGE-3168
     */
    public function deleteRootCategoryWithSubcategories()
    {
        //Data
        $rootCategoryData = $this->loadDataSet('Category', 'root_category_required');
        $subCategoryData = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category'=> $rootCategoryData['name']));
        //Steps
        $this->categoryHelper()->createCategory($rootCategoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->categoryHelper()->createCategory($subCategoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->categoryHelper()->selectCategory($rootCategoryData['name']);
        $this->categoryHelper()->deleteCategory('delete_category', 'confirm_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_category');
    }

    /**
     * Deleting Root Category with Subcategory
     *
     * @test
     * @TestlinkId TL-MAGE-3169
     */
    public function deleteRootCategoryWithSubcategoriesHavingProducts()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $rootCategoryData = $this->loadDataSet('Category', 'root_category_required');
        $subCategoryData = $this->loadDataSet('Category', 'sub_category_all',
            array('category_products_search_sku' => $productData['general_sku'],
                  'parent_category'              => $rootCategoryData['name']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->assertPreConditions();
        $this->categoryHelper()->createCategory($rootCategoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->categoryHelper()->createCategory($subCategoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->categoryHelper()->selectCategory($rootCategoryData['name']);
        $this->categoryHelper()->deleteCategory('delete_category', 'confirm_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_category');
    }
}
