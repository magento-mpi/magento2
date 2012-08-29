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
 * @license     {license_link}
 */
class Enterprise2_Mage_Product_DuplicateTest extends Mage_Selenium_TestCase
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
     * <p>Creating duplicated Gift Card</p>
     * <p>Steps:</p>
     * <p>1. Open created product;</p>
     * <p>2. Click "Duplicate" button;</p>
     * <p>3. Verify that all fields has the same data except SKU and Status(fields empty)</p>
     * <p>Expected result:</p>
     * <p>Product is duplicated, confirmation message appears;</p>
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
        $this->clickButton('duplicate');
        //Verifying
        $productData['inventory_qty'] = '0';
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $this->productHelper()->verifyProductInfo($productData, array('general_sku', 'general_status'));
    }
}