<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enable MAP On Gesture.
 *
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
        $simple = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $category['parent_category'] . '/' . $category['name']));

        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Map/enable_map_gesture');
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->flushCache();

        return array($category['name'], $simple['general_name']);
    }

    /**
     * <p>MAP is enabled "On Gesture" and link "Click for Price" is displayed on the Category page in Frontend. </p>
     *
     * @param string $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6056
     */
    public function enableMinimumAdvertisedPriceOnGesture($testData)
    {
        list($category, $product) = $testData;
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        //Verification
        $this->addParameter('productName', $product);
        $this->assertTrue($this->controlIsVisible('link', 'click_for_price'));
    }
}