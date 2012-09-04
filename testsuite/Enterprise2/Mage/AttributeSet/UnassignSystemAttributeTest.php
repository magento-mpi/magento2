<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AttributeSet
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
/**
 * Verifying the ability to unassign system attributes from attribute set
 */
class Enterprise2_Mage_AttributeSet_UnassignSystemAttributeTest
    extends Community2_Mage_AttributeSet_UnassignSystemAttributeTest
{
    /**
     * <p>DataProvider with list of not required system attributes</p>
     *
     * @return array
     */
    public function unassignedSystemAttributeDataProvider()
    {
        return array(
            array('cost'),
            array('country_of_manufacture'),
            array('custom_design'),
            array('custom_design_from'),
            array('custom_design_to'),
            array('custom_layout_update'),
            array('enable_googlecheckout'),
            array('gallery'),
            array('gift_message_available'),
            array('gift_wrapping_available'),
            array('gift_wrapping_price'),
            array('group_price'),
            array('image'),
            array('is_recurring'),
            array('is_returnable'),
            array('media_gallery'),
            array('meta_description'),
            array('meta_keyword'),
            array('meta_title'),
            array('msrp'),
            array('msrp_display_actual_price_type'),
            array('msrp_enabled'),
            array('news_from_date'),
            array('news_to_date'),
            array('open_amount_max'),
            array('open_amount_min'),
            array('options_container'),
            array('page_layout'),
            array('recurring_profile'),
            array('small_image'),
            array('special_from_date'),
            array('special_price'),
            array('special_to_date'),
            array('thumbnail'),
            array('tier_price'),
            array('url_key')
        );
    }

    /**
     * <p>DataProvider with list of non unassignable system attributes</p>
     *
     * @return array
     */
    public function nonUnassignableSystemAttributesDataProvider()
    {
        return array(
            array('allow_open_amount'),
            array('description'),
            array('giftcard_amounts'),
            array('name'),
            array('price'),
            array('price_view'),
            array('short_description'),
            array('sku'),
            array('status'),
            array('tax_class_id'),
            array('visibility'),
            array('weight')
        );
    }
}
