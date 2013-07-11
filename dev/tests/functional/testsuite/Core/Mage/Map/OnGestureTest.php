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
class Core_Mage_Map_OnGestureTest extends Mage_Selenium_TestCase
{
    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Map/disable_map');
    }

    /**
     * <p>Preconditions:</p>
     * @return string
     *
     * @test
     */
    public function preconditionsForTests()
    {
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $catPath = $category['parent_category'] . '/' . $category['name'];
        $productCat = array('general_categories' => $catPath);
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
     *
     * @param string $category
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6056
     */
    public function enableMinimumAdvertisedPriceOnGesture($category)
    {
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Map/enable_map_gesture');
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        //Verification
        $this->assertTrue($this->controlIsVisible('link', 'click_for_price'));
    }
}