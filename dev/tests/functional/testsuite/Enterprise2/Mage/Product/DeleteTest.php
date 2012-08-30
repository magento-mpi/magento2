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
 * Gift Cards deletion tests
 *
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @license     {license_link}
 */
class Enterprise2_Mage_Product_DeleteTest extends Mage_Selenium_TestCase
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
     * <p>Delete Gift Card.</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     * <p>6. Open product;</p>
     * <p>7. Click "Delete" button;</p>
     * <p>Expected result:</p>
     * <p>Product is deleted, confirmation message appears;</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5864
     */
    public function deleteGiftCard()
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
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_product');
    }

    /**
     * <p>Delete several Gift Cards.</p>
     * <p>Preconditions: Create several products</p>
     * <p>Steps:</p>
     * <p>1. Search and choose several products.</p>
     * <p>3. Select 'Actions' to 'Delete'.</p>
     * <p>2. Click 'Submit' button.</p>
     * <p>Expected result:</p>
     * <p>Products are deleted.</p>
     * <p>Success Message is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5865
     */
    public function deleteGiftCardThroughMassAction()
    {
        $productQty = 2;
        for ($i = 1; $i <= $productQty; $i++) {
            //Data
            $productData = $this->loadDataSet('Product', 'gift_card_required');
            ${'searchData' . $i} =
                $this->loadDataSet('Product', 'product_search', array('product_name' => $productData['general_sku']));
            //Steps
            $this->productHelper()->createProduct($productData, 'giftcard');
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        for ($i = 1; $i <= $productQty; $i++) {
            $this->searchAndChoose(${'searchData' . $i});
        }
        $this->addParameter('qtyDeletedProducts', $productQty);
        $xpath = $this->_getControlXpath('dropdown', 'product_massaction');
        $this->select($xpath, 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
    }
}