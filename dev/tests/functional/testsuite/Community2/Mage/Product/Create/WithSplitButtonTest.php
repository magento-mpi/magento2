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
 * Simple product creation tests
 */
class Community2_Mage_Product_Create_WithSplitButtontTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Creating simple product with required fields using "Add Product" splitbutton</p>
     * <p>Steps:</p>
     * <p>1. Click "Add Product" splitbutton</p>
     * <p>2. Fill all required fields</p>
     * <p>3. Click "Save" button</p>
     *
     * <p>Expected result:</p>
     * <p>1. New simple product creation page is opened. Default attribute set is setted by default</p>
     * <p>3.The message: "The product has been saved" appears</p>
     *
     * @TestlinkId TL-MAGE-6084
     * @test
     */
    public function createSimpleWithSplitButton()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->clickButton('add_new_product_split', false);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->addParameter('productType', $this->defineParameterFromUrl('type'));
        $this->addParameter('setId', $this->defineParameterFromUrl('set'));
        $this->validatePage();
        $this->productHelper()->fillProductInfo($productData);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }
}