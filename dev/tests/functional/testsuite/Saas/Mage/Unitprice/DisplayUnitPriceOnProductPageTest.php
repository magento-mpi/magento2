<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Goext_Mage_Unitprice_GeneralConiguration_DisplayUnitPriceOnProductPageTest
    extends Mage_Selenium_TestCase
{
    protected static $_productNameSimple;
    protected static $_productSearchSimple;
    protected static $_categoryData;

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();

        //Create test category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        self::$_categoryData = $this->loadDataSet(
            'Category', 'sub_category_required',
            array(
                'name' => 'Sub Category Required %randomize%',
                'available_product_listing_config' => '%noValue%',
                'available_product_listing' => '%noValue%'
            )
        );
        $this->categoryHelper()->createCategory(self::$_categoryData);

        //Load data for simple product
        $productDataSimple = $this->loadDataSet(
            'Product', 'simple_product_with_param_for_configurable',
            array(
                'general_name' => '%randomize% simple_product_for_order',
                'prices_price' => '100',
            )
        );

        $productDataSimple['categories'] = 'Default Category' . '/' . self::$_categoryData['name'];

        self::$_productSearchSimple = $this->loadDataSet(
            'Product', 'product_search',
            array('product_sku' => $productDataSimple['general_sku'])
        );

        //Creating simple product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productDataSimple);
        self::$_productNameSimple = $productDataSimple['general_name'];
        $this->assertMessagePresent('success', 'success_saved_product');

        //Load data for Unit Price
        $unitPriceDataSimple = $this->loadDataSet(
            'ConfigProductUnitPrice', 'unitprice_default_prod_pricetab',
            array(
                'unit_price_measure_for_base_prod_pricetab' => 'Pound (lbs)',
                'unit_price_measure_for_unit_prod_pricetab' => 'Pound (lbs)',
                'unit_price_one_item_based_prod_volume' => '10',
                'unit_price_unit_prod_volume' => '1'
            )
        );

        $this->unitpriceHelper()->setProductUnitPrice(self::$_productSearchSimple, $unitPriceDataSimple);

        $this->logoutAdminUser();
    }

    /**
     * @test
     * @dataProvider displayUnitPriceOnProductPageDataProvider
     * @TestlinkId TL-GOEXT-16,TL-GOEXT-17,TL-GOEXT-18,TL-GOEXT-19
     */
    public function displayUnitPriceOnProductPageSimpleTest(
        $expectedLabelSimple, $expectedLabelConfigurable,
        $isEnabled, $isPresentOnFront
    )
    {
        //Unitprice configuration
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $configData = $this->loadDataSet(
            'ConfigUnitPrice', 'unitprice_default_sysconf',
            array(
                'unit_price_display_on_prod_view' => $isEnabled
            )
        );
        $this->systemConfigurationHelper()->configure($configData);
        $this->logoutAdminUser();

        //Check if label exists in Product View and Category pages
        $this->unitpriceHelper()->verifyUnitPriceOnProductPage(
            $expectedLabelSimple, self::$_productNameSimple, $isPresentOnFront
        );
        $this->unitpriceHelper()->verifyUnitPriceOnCategoryPage(
            $expectedLabelSimple, self::$_categoryData['name'], self::$_productNameSimple
        );
    }

    public function displayUnitPriceOnProductPageDataProvider()
    {
        return array(
            array('$10.00 / 1 Pound (lbs)', '$10.00 / 1 Liter', 'Yes', true),
            array('$10.00 / 1 Pound (lbs)', '$10.00 / 1 Liter', 'No', false)
        );
    }
}
