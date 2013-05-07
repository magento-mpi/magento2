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
class Saas_Mage_ProductAttribute_SystemDefaultValueTest extends Core_Mage_ProductAttribute_SystemDefaultValueTest
{
    /**
     * DataProvider with system attributes list
     * Override to exclude 'prices_enable_recurring_profile'
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
