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
 * Root category creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Category_Create_RootCategoryTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Categories</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
    }

    /**
     * <p>Creating Root Category with required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Root Category" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Root Category created, success message appears</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3640
     */
    public function rootCategoryWithRequiredFieldsOnly()
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'root_category_required');
        //Steps
        $this->categoryHelper()->createCategory($categoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
    }

    /**
     * <p>Creating Root Category with all fields filling</p>
     * <p>Steps</p>
     * <p>1. Click "Add Root Category" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Root Category created, success message appears</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3638
     */
    public function rootCategoryWithAllFields()
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'root_category_all');
        //Steps
        $this->categoryHelper()->createCategory($categoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
    }

    /**
     * <p>Creating Root Category with required fields empty</p>
     * <p>Steps</p>
     * <p>1. Click "Add Root Category" button </p>
     * <p>2. Fill in necessary fields, leave required fields empty</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Root Category not created, error message appears</p>
     *
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-3639
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'root_category_required', array($emptyField => '%noValue%'));
        //Steps
        $this->categoryHelper()->createCategory($categoryData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('name', 'field'),
            array('available_product_listing', 'multiselect')
        );
    }
}
