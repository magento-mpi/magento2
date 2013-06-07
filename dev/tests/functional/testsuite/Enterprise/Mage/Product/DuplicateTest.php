<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Duplicate Gift Card tests
 *
 * @package     Mage_Product
 * @subpackage  functional_tests
 */
class Enterprise_Mage_Product_DuplicateTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Navigate to Catalog -> Manage Products
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * Creating duplicated Gift Card
     *
     * @test
     * @TestlinkId TL-MAGE-5866
     */
    public function duplicateGiftCard()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        $this->productHelper()->saveProduct('duplicate');
        //Verifying
        $productData['general_sku'] = $this->productHelper()->getGeneratedSku($productData['general_sku']);
        $productData['product_online_status'] = 'Disabled';
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $this->productHelper()->verifyProductInfo($productData, array('general_qty'));
    }
}
