<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Various
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adding product of type bundle fixed with percent options enabled for sub-products to shopping cart
 * Test added due to bug MAGE-5495 verification
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Various_AddToShoppingCartTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog - Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Adding Bundle product with Simple product to cart (Price Type = Percent)</p>
     * <p>Verification of MAGE-5495</p>
     * <p>Steps:</p>
     * <p>1. Go to Backend.</p>
     * <p>2. Create bundle fixed product. Add sub-product with Price Type = Percent.</p>
     * <p>3. Add configured bundle fixed product to the cart.</p>
     * <p>Expected Result:</p>
     * <p>Product added to the cart.</p>
     *
     * @test
     */
    public function bundleWithSimpleProductPercentPrice()
    {
        //Data
        $simpleData = $this->loadDataSet('Product', 'simple_product_visible');
        $bundleData = $this->loadDataSet('Product', 'fixed_bundle_visible');
        $bundleData['bundle_items_data']['item_1'] = $this->loadDataSet('Product', 'bundle_item_2',
            array('bundle_items_search_sku'   => $simpleData['general_sku'],
                  'selection_item_price'      => '10',
                  'selection_item_price_type' => 'Percent',));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $bundleData['general_sku']));
        $options['option_1'] = $this->loadDataSet('Product', 'bundle_options_to_add_to_shopping_cart/option_4', null,
            array('subProduct_4' => $simpleData['general_name']));
        //Steps
        $this->productHelper()->createProduct($simpleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($bundleData, 'bundle');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($bundleData);
        //Steps
        $this->frontend();
        $this->productHelper()->frontOpenProduct($bundleData['general_name']);
        $this->productHelper()->frontAddProductToCart($options);
        //Verifying
        $this->validatePage('shopping_cart');
        $this->assertFalse($this->isTextPresent('Internal server error', 'HTTP Error 500 Internal server error'));
    }
}