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
 */
class Enterprise_Mage_Product_DeleteTest extends Mage_Selenium_TestCase
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
            $this->searchAndChoose(${'searchData' . $i}, 'product_grid');
        }
        $this->addParameter('qtyDeletedProducts', $productQty);
        $this->fillDropdown('mass_action_select_action', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
    }
}
