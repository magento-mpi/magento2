<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminUser
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products updating using batch updates tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_BatchUpdates_Products_MassActionTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Deleting products using Batch Updates Negative test</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5954
     */
    public function deleteNegative()
    {
        //Steps
        $this->fillDropdown('product_massaction', 'Delete');
        $this->clickButton('submit', false);
        //Verifying
        $this->assertSame('Please select items.', $this->alertText(), 'actual and expected confirmation message does not
        match');
        $this->acceptAlert();
    }

    /**
     * <p>Change status products using Batch Updates Negative test</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5949
     */
    public function changeStatusNegative()
    {
        //Steps
        $this->fillDropdown('product_massaction', 'Change status');
        $this->fillDropdown('product_status', 'Disabled');
        $this->clickButton('submit', false);
        //Verifying
        $this->assertSame('Please select items.', $this->alertText(), 'actual and expected confirmation message does not
        match');
        $this->acceptAlert();
    }

    /**
     * <p>Update Attributes products using Batch Updates Negative test</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5953
     */
    public function updateAttributesNegative()
    {
        //Steps
        $this->fillDropdown('product_massaction', 'Update Attributes');
        $this->clickButton('submit', false);
        //Verifying
        $this->assertSame('Please select items.', $this->alertText(), 'actual and expected confirmation message does not
        match');
        $this->acceptAlert();
    }

    /**
     * <p>Updating created products using Batch Updates</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5950
     */

    public function changeStatus()
    {
        $productQty = 2;
        for ($i = 1; $i <= $productQty; $i++) {
            //Data
            $productData = $this->loadDataSet('Product', 'simple_product_required');
            ${'searchData' . $i} =
                $this->loadDataSet('Product', 'product_search', array('product_name' => $productData['general_sku']));
            //Steps
            $this->productHelper()->createProduct($productData);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        for ($i = 1; $i <= $productQty; $i++) {
            $this->searchAndChoose(${'searchData' . $i}, 'product_grid');
        }
        $this->addParameter('qtyUpdatedProducts', $productQty);
        $this->fillDropdown('product_massaction', 'Change status');
        $this->fillDropdown('product_status', 'Disabled');
        $this->addParameter('storeId', '');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_updated_products_status_massaction');
    }

    /**
     * <p>Updating products attributes using Batch Updates</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5952
     */
    public function updateAllProductsFields()
    {
        $this->markTestIncomplete('MAGETWO-3250');
        $productQty = 2;
        for ($i = 1; $i <= $productQty; $i++) {
            //Data
            $productData = $this->loadDataSet('Product', 'simple_product_required');
            ${'searchData' . $i} =
                $this->loadDataSet('Product', 'product_search', array('product_name' => $productData['general_sku']));
            //Steps
            $this->productHelper()->createProduct($productData);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        for ($i = 1; $i <= $productQty; $i++) {
            $this->searchAndChoose(${'searchData' . $i}, 'manage_products');
        }
        $this->addParameter('qtyUpdatedAtrProducts', $productQty);
        $this->fillDropdown('product_massaction', 'Update Attributes');
        $this->addParameter('storeId', '0');
        $this->clickButton('submit');
        //Data
        $dataForAttributesTab = $this->loadDataSet('Product', 'product_update_attributes_tab');
        $dataForInventoryTab = $this->loadDataSet('Product', 'product_update_inventory_tab');
        $dataForWebsitesTab = $this->loadDataSet('Product', 'product_update_websites_tab');
        //Steps
        $this->productHelper()->updateThroughMassAction($dataForAttributesTab, $dataForInventoryTab,
        $dataForWebsitesTab);
        $this->clickButton('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_updated_products_attributes_massaction');
    }
}