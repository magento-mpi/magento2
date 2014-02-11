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
 * Category Move Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Category_MoveTest extends Mage_Selenium_TestCase
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
     * Move Root Category to Root category.
     *
     * @test
     * @TestlinkId TL-MAGE-3173
     */
    public function rootCategoryToRoot()
    {
        //Data
        $categoryDataFrom = $this->loadDataSet('Category', 'root_category_required');
        $categoryDataTo = $this->loadDataSet('Category', 'root_category_required');
        //Steps
        $this->categoryHelper()->createCategory($categoryDataFrom);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->categoryHelper()->createCategory($categoryDataTo);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->categoryHelper()->moveCategory($categoryDataFrom['name'], $categoryDataTo['name']);
        //Verification
        $this->categoryHelper()->selectCategory($categoryDataTo['name'] . '/' . $categoryDataFrom['name']);
    }

    /**
     * Move Root with Sub Category to Root category.
     *
     * @test
     * @TestlinkId TL-MAGE-3174
     */
    public function rootWithSubToRoot()
    {
        //Data
        $categoryDataFrom = $this->loadDataSet('Category', 'root_category_required');
        $categoryDataSub = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category' => $categoryDataFrom['name']));
        $categoryDataTo = $this->loadDataSet('Category', 'root_category_required');
        //Steps
        $this->categoryHelper()->createCategory($categoryDataFrom);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->createCategory($categoryDataSub);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->createCategory($categoryDataTo);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->moveCategory($categoryDataFrom['name'], $categoryDataTo['name']);
        //Verification
        $this->categoryHelper()->selectCategory(
            $categoryDataTo['name'] . '/' . $categoryDataFrom['name'] . '/' . $categoryDataSub['name']);
    }

    /**
     * Move Sub Category to Sub category.
     *
     * @test
     * @TestlinkId TL-MAGE-3175
     */
    public function subToSubNestedCategory()
    {
        //Data
        $categoryDataFrom = $this->loadDataSet('Category', 'root_category_required');
        $categoryDataSubFrom = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category' => $categoryDataFrom['name']));
        $categoryDataTo = $this->loadDataSet('Category', 'root_category_required');
        $categoryDataSubTo = $this->loadDataSet('Category', 'sub_category_required',
            array('parent_category' => $categoryDataTo['name']));
        //Steps
        $this->categoryHelper()->createCategory($categoryDataFrom);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->createCategory($categoryDataSubFrom);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->createCategory($categoryDataTo);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->createCategory($categoryDataSubTo);
        $this->categoryHelper()->moveCategory($categoryDataSubFrom['name'], $categoryDataSubTo['name']);
        //Verification
        $this->categoryHelper()->selectCategory(
            $categoryDataTo['name'] . '/' . $categoryDataSubTo['name'] . '/' . $categoryDataSubFrom['name']);
    }

    /**
     * Move Root Category assigned to store to Root category.
     *
     * @test
     * @TestlinkId TL-MAGE-3172
     */
    public function rootCategoryAssignedToWebsite()
    {
        //Data
        $categoryDataFrom = $this->loadDataSet('Category', 'root_category_required');
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('website'       => $websiteData['website_name'],
                                                                        'root_category' => $categoryDataFrom['name']));
        $categoryDataTo = $this->loadDataSet('Category', 'root_category_required');
        //Create categories
        $this->categoryHelper()->createCategory($categoryDataFrom);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->createCategory($categoryDataTo);
        $this->assertMessagePresent('success', 'success_saved_category');
        //Create Website and Store. Assign root category to store
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($websiteData, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        //Try to move assigned to store root category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->moveCategory($categoryDataFrom['name'], $categoryDataTo['name']);
        //Verification
        $this->categoryHelper()->selectCategory($categoryDataFrom['name']);
    }
}
