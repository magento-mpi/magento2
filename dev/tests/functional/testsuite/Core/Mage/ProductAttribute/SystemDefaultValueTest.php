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
class Core_Mage_ProductAttribute_SystemDefaultValueTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Navigate to System - Manage Attributes.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
    }

    /**
     * Default value for System attributes
     * Preconditions:
     * 1. System attribute is existed.
     * 2. System attribute is presented in Default attribute set.
     *
     * @param string $attributeCode
     * @param string $productType
     * @param string $uimapName
     *
     * @test
     * @dataProvider systemAttributeDataProvider
     * @TestlinkId TL-MAGE-5749, TL-MAGE-5750, TL-MAGE-5751, TL-MAGE-5752, TL-MAGE-5753, TL-MAGE-5754,
     *             TL-MAGE-5755, TL-MAGE-5756, TL-MAGE-5757, TL-MAGE-5758, TL-MAGE-5759, TL-MAGE-5760,
     *             TL-MAGE-5761, TL-MAGE-5762, TL-MAGE-5835, TL-MAGE-5836
     */
    public function checkDefaultValue($attributeCode, $productType, $uimapName)
    {
        //Data
        $attributeData = $this->loadDataSet('SystemAttributes', $attributeCode);
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data',
            array('attribute_code' => $attributeData['advanced_attribute_properties']['attribute_code']));
        if ($attributeCode == 'status') {
            $searchData['attribute_label'] = 'Status';
        }
        //Steps
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifySystemAttribute($attributeData);
        if (isset($attributeData['set_default_value'])) {
            $this->saveAndContinueEdit('button', 'save_and_continue_edit');
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_attribute');
            $this->addParameter('optionName', $attributeData['set_default_value']);
            $this->assertTrue($this->getControlAttribute('checkbox', 'default_value_by_option_name', 'selectedValue'),
                'Option with value "' . $attributeData['default_value'] . '" is not set as default for attribute');
        }
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->selectTypeProduct($productType);
        //Verifying
        $verifyData[$uimapName] = (isset($attributeData['set_default_value']))
            ? $attributeData['set_default_value']
            : $attributeData['default_value'];
        $this->productHelper()->verifyProductInfo($verifyData);
    }

    /**
     * DataProvider with system attributes list
     *
     * @return array
     */
    public function systemAttributeDataProvider()
    {
        return array(
            array('country_of_manufacture', 'simple', 'autosettings_country_manufacture'),
            array('custom_design', 'simple', 'design_custom_design'),
            array('enable_googlecheckout', 'simple', 'prices_enable_googlecheckout'),
            array('gift_message_available', 'simple', 'autosettings_allow_gift_message'),
            array('is_recurring', 'simple', 'prices_enable_recurring_profile'),
            array('msrp_enabled', 'simple', 'prices_apply_map'),
            array('msrp_display_actual_price_type', 'simple', 'prices_display_actual_price'),
            array('options_container', 'simple', 'design_display_product_options_in'),
            array('page_layout', 'simple', 'design_page_layout'),
            array('price_view', 'bundle', 'general_price_view_bundle'),
            array('status', 'simple', 'product_online_status'),
            array('tax_class_id', 'simple', 'general_tax_class'),
            array('visibility', 'simple', 'autosettings_visibility')
        );
    }
}
