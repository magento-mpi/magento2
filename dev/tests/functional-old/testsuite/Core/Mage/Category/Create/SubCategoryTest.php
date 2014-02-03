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
     * Preconditions:
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
    }

    /**
     * Creating Subcategory with required fields
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
     * Creating Subcategory with all fields filling
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
     * Creating Subcategory with required fields empty
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
     * Creating Subcategory with special characters
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
     * Creating Subcategory with long values in required fields
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
     * Creating nested Subcategory with required fields
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
