<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Check the possibility to set default value to system attributes with dropdown type
 */
class Enterprise2_Mage_ProductAttribute_SystemDefaultValueTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System - Manage Attributes.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
    }

    /**
     * <p>Default value for System attributes</p>
     * <p>Preconditions:</p>
     *  <p>1. System attribute is existed.</p>
     *  <p>2. System attribute is presented in Default attribute set.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Login to backend as admin</p>
     *  <p>2. Go to Catalog - Attributes - Manage Attributes</p>
     *  <p>3. Enter attribute code  in Attribute Code search field and press the "Search" button</p>
     *  <p>4. Open attribute.</p>
     *  <p>5. Click on Manage Label / Options tab</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Attribute options is not editable and not present such buttons:</p>
     *  <p>1.1. "Add option" button</p>
     *  <p>1.2. "Delete" button</p>
     *
     * <p>Steps:</p>
     *  <p>6. Select default attribute option.</p>
     *  <p>7. Press the "Save Attribute" button</p>
     *  <p>8. Go to Catalog - Manage Products</p>
     *  <p>9. Press the "Add Product" button</p>
     *  <p>10. Select "Default" as "Attribute Set".</p>
     *  <p>11. Select "Simple Product" as "Product Type"</p>
     *  <p>12. Press the "Continue" button</p>
     *  <p>13. Verify that system attribute is used Default value and fill all required field, and press the "Save" button</p>
     *  <p>14. Find created product in grid and click on it.</p>
     *
     * <p>Expected result:</p>
     *  <p>1. Product page opens with saved changes. For system attribute default value is selected.</p>
     *
     * @param string $attr
     * @param string $productType
     * @param string $uimapName
     *
     * @test
     * @dataProvider systemAttributeDataProvider
     * @TestlinkId TL-MAGE-5749, TL-MAGE-5750, TL-MAGE-5751, TL-MAGE-5752, TL-MAGE-5753, TL-MAGE-5754, TL-MAGE-5755, TL-MAGE-5756, TL-MAGE-5757, TL-MAGE-5758, TL-MAGE-5759, TL-MAGE-5760, TL-MAGE-5761, TL-MAGE-5762, TL-MAGE-5835, TL-MAGE-5836
     */
    public function checkSystemAttributeDefaultValue($attr, $productType, $uimapName)
    {
        //Data
        $attrData = $this->loadDataSet('SystemAttributes', $attr);
        $productData = $this->loadDataSet('Product', $productType . '_product_required');
        if (array_key_exists($uimapName, $productData)) {
            unset($productData[$uimapName]);
        }
        //Steps
        $this->searchAndOpen(array('attribute_code' => $attrData['attribute_code']));
        //Verifying
        $this->productAttributeHelper()->verifySystemAttribute($attrData);
        //Steps
        $this->productAttributeHelper()->setDefaultAttributeValue($attrData);
        $this->saveForm('save_attribute');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->searchAndOpen(array('product_sku' => $productData['general_sku']));
        //Verifying
        if ($attr == 'custom_design') {
            $this->openTab('design');
            $this->assertEquals(strtolower(str_replace(' ', '', $attrData['default_value'])),
                $this->getValue($this->_getControlXpath('dropdown', $uimapName) . "//option[@selected='selected']"),
                "Incorrect default value for custom design attribute.");
        } else {
            $productData[$uimapName] = $attrData['default_value'];
        }
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
    }

    /**
     * <p>DataProvider with system attributes list</p>
     *
     * @return array
     */
    public function systemAttributeDataProvider()
    {
        return array(array('allow_open_amount', 'gift', 'price_allow_open_amount'), //waiting for gift card realization
                     array('country_of_manufacture', 'simple', 'general_country_manufacture'),
                     array('custom_design', 'simple', 'design_custom_design'),
                     array('enable_googlecheckout', 'simple', 'prices_enable_googlecheckout'),
                     array('gift_message_available', 'simple', 'gift_options_allow_gift_message'),
                     array('gift_wrapping_available', 'simple', 'gift_options_allow_gift_wrapping'),
                     array('is_recurring', 'simple', 'recurring_profile_enable_recurring_profile'),
                     array('is_returnable', 'simple', 'general_enable_rma'),
                     array('msrp_enabled', 'simple', 'prices_apply_map'),
                     array('msrp_display_actual_price_type', 'simple', 'prices_display_actual_price'),
                     array('options_container', 'simple', 'design_display_product_options_in'),
                     array('page_layout', 'simple', 'design_page_layout'),
                     array('price_view', 'bundle', 'prices_price_view_bundle'),
                     array('status', 'simple', 'general_status'),
                     array('tax_class_id', 'simple', 'prices_tax_class'),
                     array('visibility', 'simple', 'general_visibility')
        );
    }
}
