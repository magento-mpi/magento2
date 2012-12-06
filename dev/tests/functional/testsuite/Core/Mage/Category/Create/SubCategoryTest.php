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
 * Sub category creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Category_Create_SubCategoryTest extends Mage_Selenium_TestCase
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
     * <p>Creating Subcategory with required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @return string
     * @test
     * @TestlinkId TL-MAGE-3645
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        //Steps
        $this->categoryHelper()->createCategory($categoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();

        return $categoryData['parent_category'] . '/' . $categoryData['name'];
    }

    /**
     * <p>Creating Subcategory with all fields filling</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @param string $rooCat
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3642
     */
    public function subCategoryWithAllFields($rooCat)
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_all', array('parent_category' => $rooCat));
        //Steps
        $this->categoryHelper()->createCategory($categoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
    }

    /**
     * <p>Creating Subcategory with required fields empty</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in necessary fields, leave required fields empty</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory not created, error message appears</p>
     *
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3644
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_required', array($emptyField => '%noValue%'));
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

    /**
     * <p>Creating Subcategory with special characters</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields with special characters</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @param string $rooCat
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3644
     */
    public function withSpecialCharacters($rooCat)
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_required',
            array('name'            => $this->generate('string', 32, ':punct:'),
                  'parent_category' => $rooCat));
        //Steps
        $this->categoryHelper()->createCategory($categoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
    }

    /**
     * <p>Creating Subcategory with long values in required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @param string $rooCat
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3644
     */
    public function withLongValues($rooCat)
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_required',
            array('name'            => $this->generate('string', 255, ':alnum:'),
                  'parent_category' => $rooCat));
        //Steps
        $this->categoryHelper()->createCategory($categoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
    }

    /**
     * <p>Creating nested Subcategory with required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Subcategory" button </p>
     * <p>2.Select existing "Category Path"</p>
     * <p>3. Fill in required fields</p>
     * <p>4. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Subcategory created, success message appears</p>
     *
     * @param string $rooCat
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3641
     */
    public function nestedSubCategory($rooCat)
    {
        for ($i = 1; $i <= 10; $i++) {
            //Data
            $categoryData = $this->loadDataSet('Category', 'sub_category_required', array('parent_category'=> $rooCat));
            //Steps
            $this->categoryHelper()->createCategory($categoryData);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_category');
            $this->categoryHelper()->checkCategoriesPage();
            //Steps
            $rooCat .= '/' . $categoryData['name'];
        }
    }
}
