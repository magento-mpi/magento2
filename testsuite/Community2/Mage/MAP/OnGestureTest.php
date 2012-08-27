<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_MAP
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enable MAP On Gesture.
 *
 * @package     Mage_MAP
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_MAP_OnGestureTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Create subcategory.</p>
     * <p>Create simple product.</p>
     * @return string`
     *
     * @test
     */
    public function preconditionsForTests()
    {
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $catPath = $category['parent_category'] . '/' . $category['name'];
        $productCat = array('categories' => $catPath);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->flushCache();

        return $category['name'];
    }

    /**
     * <p>MAP is enabled "On Gesture" and link "Click for Price" is displayed on the Category page in Frontend. </p>
     * <p>Steps:</p>
     * <p>1. Login to admin</p>
     * <p>2. Fill in all required fields.</p>
     * <p>3. Click 'Save Config' button.</p>
     * <p>4. Navigate to Frontend in the Category page.</p>
     * <p>Expected result:</p>
     * <p>Product price is hidden and the "Click for price" link is displayed.</p>
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6056
     */
    public function enableMinimumAdvertisedPriceOnGesture($category)
    {
        //Data
        $config = $this->loadDataSet('MAP', 'enable_map_gesture');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        //Verification
        $this->assertTrue($this->controlIsVisible('link', 'click_for_price'));
    }
}
