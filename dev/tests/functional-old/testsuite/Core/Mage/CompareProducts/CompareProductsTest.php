<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CompareProducts
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Compare Products tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CompareProducts_CompareProductsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->frontend();
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->clickControl('link', 'about_us');
        $this->assertTrue($this->compareProductsHelper()->frontClearAll());
    }

    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_right_column');
        $path = $category['parent_category'] . '/' . $category['name'];
        $simple = $this->loadDataSet('Product', 'compare_simple_product', array('general_categories' => $path));
        $virtual = $this->loadDataSet('Product', 'compare_virtual_product', array('general_categories' => $path));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($virtual, 'virtual');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->flushCache();
        return array(
            'catName' => $category['name'],
            'names' => array($simple['general_name'], $virtual['general_name']),
            'verify' => array(
                'product_1_name' => $simple['general_name'],
                'product_1_sku' => $simple['general_sku'],
                'product_2_name' => $virtual['general_name'],
                'product_2_sku' => $virtual['general_sku']
            )
        );
    }

    /**
     * <p>Adds a product to Compare Products from Product Details page.</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3234
     */
    public function addProductToCompareListFromProductPage($data)
    {
        $verify = $this->loadDataSet('CompareProducts', 'verify_compare_data', null, $data['verify']);
        //Steps and Verifying
        foreach ($data['names'] as $value) {
            $this->compareProductsHelper()->frontAddToCompareFromProductPage($value);
            $this->assertMessagePresent('success', 'product_added_to_comparison');
            $this->assertTrue(
                $this->controlIsVisible('link', 'compare_product_link'),
                "'$value' product is not available in Compare widget"
            );
        }
        //Steps
        $this->frontend('compare_products');
        //Verifying
        $this->compareProductsHelper()->frontVerifyProductDataInComparePopup($verify);
    }

    /**
     * <p>Adds a products to Compare Products from Category page.</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3233
     */
    public function addProductToCompareListFromCatalogPage($data)
    {
        $verify = $this->loadDataSet('CompareProducts', 'verify_compare_data', null, $data['verify']);
        //Steps and Verifying
        foreach ($data['names'] as $value) {
            $this->compareProductsHelper()->frontAddToCompareFromCatalogPage($value, $data['catName']);
            $this->assertMessagePresent('success', 'product_added_to_comparison');
            $this->assertTrue(
                $this->controlIsVisible('link', 'compare_product_link'),
                "'$value' product is not available in Compare widget"
            );
        }
        //Steps
        $this->frontend('compare_products');
        //Verifying
        $this->compareProductsHelper()->frontVerifyProductDataInComparePopup($verify);
    }

    /**
     * <p>Remove a product from CompareProducts block</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3236
     */
    public function removeProductFromCompareBlockList($data)
    {
        //Steps and Verifying
        foreach ($data['names'] as $value) {
            $this->compareProductsHelper()->frontAddToCompareFromCatalogPage($value, $data['catName']);
            $this->assertMessagePresent('success', 'product_added_to_comparison');
            $this->compareProductsHelper()->frontRemoveProductFromCompareBlock($value);
            $this->assertMessagePresent('success', 'product_removed_from_comparison');
            $this->assertFalse(
                $this->controlIsVisible('link', 'compare_product_link'),
                'There is unexpected product in Compare Products widget'
            );
        }
    }

    /**
     * <p>Compare Products block is not displayed without products</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3235
     */
    public function emptyCompareListIsNotAvailable($data)
    {
        //Steps
        foreach ($data['names'] as $value) {
            $this->compareProductsHelper()->frontAddToCompareFromProductPage($value);
            $this->assertMessagePresent('success', 'product_added_to_comparison');
            $this->assertTrue(
                $this->controlIsVisible('link', 'compare_product_link'),
                "'$value' product is not available in Compare widget"
            );
        }
        //Steps
        $this->compareProductsHelper()->frontClearAll();
        $this->assertMessagePresent('success', 'compare_list_cleared');
        //Verifying
        $this->assertTrue(
            $this->controlIsVisible('pageelement', 'compare_block_empty'),
            'There is unexpected product(s) in Compare Products widget'
        );
    }
}