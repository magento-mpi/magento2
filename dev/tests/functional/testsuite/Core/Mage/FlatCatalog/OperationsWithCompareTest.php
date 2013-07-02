<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_FlatCatalog
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
class Core_Mage_FlatCatalog_OperationsWithCompareTest extends Mage_Selenium_TestCase
{
    //Id of the compare pop-up window to close.
    protected static $_popupId = null;

    public function setUpBeforeTests()
    {
        $this->markTestIncomplete('MAGETWO-8719');
        $this->loginAdminUser();
        $this->reindexInvalidedData();
        $this->navigate('system_configuration');
        $flatCatalogData = $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend',
            array('use_flat_catalog_product' => 'Yes'));
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->reindexInvalidedData();
    }

    protected function assertPreConditions()
    {
        self::$_popupId = null;
        $this->frontend('about_us');
        $this->assertTrue($this->compareProductsHelper()->frontClearAll());
    }

    protected function tearDownAfterTest()
    {
        if (self::$_popupId) {
            $this->compareProductsHelper()->frontCloseComparePopup(self::$_popupId);
        }
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $flatCatalogData =
            $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend', array('use_flat_catalog_product' => 'No'));
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->reindexInvalidedData();
    }

    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
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
            'names' => array(
                $simple['general_name'],
                $virtual['general_name']
            ),
            'verify' => array(
                'product_1_name' => $simple['general_name'],
                'product_1_sku' => $simple['general_sku'],
                'product_2_name' => $virtual['general_name'],
                'product_2_sku' => $virtual['general_sku']
            )
        );
    }

    /**
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2020, TL-MAGE-2021
     */
    public function addProductToCompareListFromProductPage($data)
    {
        //Data
        $verify = $this->loadDataSet('CompareProducts', 'verify_compare_data', null, $data['verify']);
        //Steps
        foreach ($data['names'] as $value) {
            $this->compareProductsHelper()->frontAddToCompareFromProductPage($value);
            //Verifying
            $this->assertMessagePresent('success', 'product_added_to_comparison');
            $this->frontend('about_us');
            $this->assertTrue($this->controlIsPresent('link', 'compare_product_link'),
                'Product is not available in Compare widget');
        }
        //Steps
        self::$_popupId = $this->compareProductsHelper()->frontOpenComparePopup();
        //Verifying
        $this->compareProductsHelper()->frontVerifyProductDataInComparePopup($verify);
    }

    /**
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId @TestlinkId TL-MAGE-2020, TL-MAGE-2021
     */
    public function addProductToCompareListFromCatalogPage($data)
    {
        //Steps
        foreach ($data['names'] as $value) {
            $this->compareProductsHelper()->frontAddToCompareFromCatalogPage($value, $data['catName']);
            //Verifying
            $this->assertMessagePresent('success', 'product_added_to_comparison');
            $this->assertTrue($this->controlIsPresent('link', 'compare_product_link'),
                'Product is not available in Compare widget');
            self::$_popupId = $this->compareProductsHelper()->frontOpenComparePopup();
            $this->assertTrue($this->controlIsPresent('link', 'product_title'),
                'There is no expected product in Compare Products popup');
            $this->compareProductsHelper()->frontCloseComparePopup(self::$_popupId);
        }
        self::$_popupId = null;
    }

    /**
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3236
     */
    public function removeProductFromCompareBlockList($data)
    {
        //Steps
        foreach ($data['names'] as $value) {
            $this->compareProductsHelper()->frontAddToCompareFromCatalogPage($value, $data['catName']);
            $this->assertMessagePresent('success', 'product_added_to_comparison');
            //Verifying
            $this->compareProductsHelper()->frontRemoveProductFromCompareBlock($value);
            $this->assertMessagePresent('success', 'product_removed_from_comparison');
            $this->assertFalse($this->controlIsPresent('link', 'compare_product_link'),
                'There is unexpected product in Compare Products widget');
        }
    }

    /**
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3235
     */
    public function emptyCompareListIsNotAvailable($data)
    {
        //Steps
        $this->compareProductsHelper()->frontAddToCompareFromCatalogPage($data['names'][0], $data['catName']);
        //Verifying
        $this->assertMessagePresent('success', 'product_added_to_comparison');
        $this->assertTrue($this->controlIsPresent('link', 'compare_product_link'),
            'Product is not available in Compare widget');
        //Steps
        $this->compareProductsHelper()->frontClearAll();
        $this->assertMessagePresent('success', 'compare_list_cleared');
        //Verifying
        $this->assertTrue($this->controlIsPresent('pageelement', 'compare_block_empty'),
            'There is unexpected product(s) in Compare Products widget');
    }
}