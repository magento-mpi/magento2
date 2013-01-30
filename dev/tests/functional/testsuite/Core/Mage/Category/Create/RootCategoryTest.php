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
     * Preconditions:
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
    }

    /**
     * Creating Root Category with required fields
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
     * Creating Root Category with all fields filling
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
     * Creating Root Category with required fields empty
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
