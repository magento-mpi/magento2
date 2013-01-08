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
class Enterprise_Mage_ProductAttribute_SystemDefaultValueTest
    extends Core_Mage_ProductAttribute_SystemDefaultValueTest
{
    /**
     * <p>DataProvider with system attributes list</p>
     *
     * @return array
     */
    public function systemAttributeDataProvider()
    {
        return array(
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
