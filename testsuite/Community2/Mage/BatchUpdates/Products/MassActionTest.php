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
class Community2_Mage_BatchUpdates_Products_MassActionTest extends Mage_Selenium_TestCase
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
     * <p>Deleting products using Batch Updates Negative test</p>
     * <p>Steps:</p>
     * <p>1. Select value "Delete" in "Action" dropdown</p>
     * <p>2. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the popup message "Please select items.".</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5954
     */
    public function deleteNegative()
    {
        //Steps
        $this->fillDropdown('product_massaction', 'Delete');
        $this->clickButton('submit', false);
        if (!$this->isAlertPresent()) {
            $this->fail('confirmation message not found on page');
        }
        $actualAlertText = $this->getAlert();
        //Verifying
        $this->assertSame('Please select items.', $actualAlertText, 'actual and expected confirmation message does not
        match');
    }

    /**
     * <p>Change status products using Batch Updates Negative test</p>
     * <p>Steps:</p>
     * <p>1. Select value "Change status" in "Action" dropdown</p>
     * <p>2. Select value "Disabled" in "Status" dropdown</p>
     * <p>3. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the popup message "Please select items.".</p>
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
        if (!$this->isAlertPresent()) {
            $this->fail('confirmation message not found on page');
        }
        $actualAlertText = $this->getAlert();
        //Verifying
        $this->assertSame('Please select items.', $actualAlertText, 'actual and expected confirmation message does not
        match');
    }

    /**
     * <p>Update Attributes products using Batch Updates Negative test</p>
     * <p>Steps:</p>
     * <p>1. Select value "Update Attribute" in "Action" dropdown</p>
     * <p>2. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the popup message "Please select items.".</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5953
     */
    public function updateAttributesNegative()
    {
        //Steps
        $this->fillDropdown('product_massaction', 'Update Attributes');
        $this->clickButton('submit', false);
        if (!$this->isAlertPresent()) {
            $this->fail('confirmation message not found on page');
        }
        $actualAlertText = $this->getAlert();
        //Verifying
        $this->assertSame('Please select items.', $actualAlertText, 'actual and expected confirmation message does not
        match');
    }

    /**
     * <p>Updating created products using Batch Updates</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add Product"</p>
     * <p>2. Fill in the all required fields</p>
     * <p>3. Click button "Save"</p>
     * <p>4. Click button "Add Product"</p>
     * <p>5. Fill in the all required fields</p>
     * <p>6. Click button "Save"</p>
     * <p>7. Select created products by checkboxes"</p>
     * <p>8. Select value "Change Status" in "Action" dropdown</p>
     * <p>9. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the products has been updated.</p>
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
            $this->searchAndChoose(${'searchData' . $i});
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
     * <p>Steps:</p>
     * <p>1. Click button "Add Product"</p>
     * <p>2. Fill in the all required fields</p>
     * <p>3. Click button "Save"</p>
     * <p>4. Click button "Add Product"</p>
     * <p>5. Fill in the all required fields</p>
     * <p>6. Click button "Save"</p>
     * <p>7. Select created products by checkboxes"</p>
     * <p>8. Select value "Update Attributes" in "Action" dropdown</p>
     * <p>9. Click button "Submit"</p>
     * <p>10. Fill in the all fields"</p>
     * <p>11. Click button "Save""</p>
     * <p>Expected result:</p>
     * <p>Received the message that the products has been updated.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5952
     */
    public function updateAllProductsFields()
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
            $this->searchAndChoose(${'searchData' . $i});
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
        $this->productsHelper()->updateThroughMassAction($dataForAttributesTab, $dataForInventoryTab,
        $dataForWebsitesTab);
        $this->clickButton('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_updated_products_attributes_massaction');
    }
}