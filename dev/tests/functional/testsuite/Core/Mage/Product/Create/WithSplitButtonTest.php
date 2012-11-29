<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product creation tests
 */
class Core_Mage_Product_Create_WithSplitButtonTest extends Mage_Selenium_TestCase
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
     * <p>Creating simple product with required fields using "Add Product" split button</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6084
     */
    public function createSimpleWithSplitButton()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->clickButton('add_new_product_split', false);
        $this->waitForPageToLoad();
        $this->addParameter('productType', $this->defineParameterFromUrl('type'));
        $this->addParameter('setId', $this->defineParameterFromUrl('set'));
        $this->validatePage();
        $this->productHelper()->fillProductInfo($productData);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku'],
            'product_type' => 'Simple Product'));
        $this->productHelper()->verifyProductInfo($productData);
    }
}