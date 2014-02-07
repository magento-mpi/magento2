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
class Core_Mage_BatchUpdates_Product_MassActionTest extends Mage_Selenium_TestCase
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
        $this->fillDropdown('mass_action_select_action', 'Delete');
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
        $this->fillDropdown('mass_action_select_action', 'Change status');
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
        $this->fillDropdown('mass_action_select_action', 'Update Attributes');
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
        $searchData = array();
        for ($i = 1; $i <= $productQty; $i++) {
            $productData = $this->loadDataSet('Product', 'simple_product_required');
            $searchData[$i] = $this->loadDataSet('Product', 'product_search',
                array('product_name' => $productData['general_sku']));
            $this->productHelper()->createProduct($productData);
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        for ($i = 1; $i <= $productQty; $i++) {
            $this->searchAndChoose($searchData[$i], 'product_grid');
        }
        $this->addParameter('qtyUpdatedProducts', $productQty);
        $this->fillDropdown('mass_action_select_action', 'Change status');
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
        $this->markTestIncomplete('BUG: "Use Default" jquery-widget does not work correct');
        $productQty = 2;
        $searchData = array();
        for ($i = 1; $i <= $productQty; $i++) {
            $productData = $this->loadDataSet('Product', 'simple_product_required');
            $searchData[$i] = $this->loadDataSet('Product', 'product_search',
                array('product_name' => $productData['general_sku']));
            $this->productHelper()->createProduct($productData);
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        foreach ($searchData as $search) {
            $this->searchAndChoose($search, 'product_grid');
        }
        $this->addParameter('qtyUpdatedAtrProducts', $productQty);
        $this->fillDropdown('mass_action_select_action', 'Update Attributes');
        $this->clickButton('submit');
        //Data
        $attributesTab = $this->loadDataSet('Product', 'product_update_attributes_tab');
        $inventoryTab = $this->loadDataSet('Product', 'product_update_inventory_tab');
        $websitesTab = $this->loadDataSet('Product', 'product_update_websites_tab');
        //Steps
        $this->productHelper()->updateThroughMassAction($attributesTab, $inventoryTab, $websitesTab);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_updated_products_attributes_massaction');
    }

    /**
     * Verifying product type switcher, base image control, category selector are absent using Batch Updates
     *
     * @test
     * @dataProvider excludedAttributesDataProvider
     * @Testlink TL-MAGE-6660
     * @param array $excludedAttributes
     */
    public function verifyExcludedAttributesAreAbsent($excludedAttributes)
    {
        $productQty = 2;
        $searchData = array();
        for ($i = 1; $i <= $productQty; $i++) {
            $productData = $this->loadDataSet('Product', 'simple_product_required');
            $searchData[$i] = $this->loadDataSet('Product', 'product_search',
                array('product_name' => $productData['general_sku']));
            $this->productHelper()->createProduct($productData);
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        for ($i = 1; $i <= $productQty; $i++) {
            $this->searchAndChoose($searchData[$i], 'product_grid');
        }
        $this->addParameter('qtyUpdatedAtrProducts', $productQty);
        $this->fillDropdown('mass_action_select_action', 'Update Attributes');
        $this->addParameter('storeId', '0');
        $this->clickButton('submit');
        //Verifying
        foreach ($excludedAttributes as $controlName => $controlType) {
            $this->assertFalse($this->controlIsVisible($controlType, $controlName));
        }
    }

    /**
     * @return array
     */
    public static function excludedAttributesDataProvider()
    {
        return array(array(array(
            'attributes_image' => self::FIELD_TYPE_INPUT,
            'attributes_category' => self::FIELD_TYPE_INPUT,
            'attributes_change_category' => self::FIELD_TYPE_CHECKBOX,
            'attributes_weight_and_type_switcher' => self::FIELD_TYPE_CHECKBOX,
            'attributes_change_image_gallery' => self::FIELD_TYPE_CHECKBOX,
            'attributes_change_group_price' => self::FIELD_TYPE_CHECKBOX,
            'attributes_change_tier_price' => self::FIELD_TYPE_CHECKBOX
        )));
    }
}