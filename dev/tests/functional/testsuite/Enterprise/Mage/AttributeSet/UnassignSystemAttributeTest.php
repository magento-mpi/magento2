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
class Enterprise_Mage_AttributeSet_UnassignSystemAttributeTest
    extends Core_Mage_AttributeSet_UnassignSystemAttributeTest
{
    /**
     * DataProvider for system attributes, which can be unassigned
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
            array('gift_wrapping_available'),
            array('gift_wrapping_price'),
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
            array('url_key')
        );
    }

    /**
     * DataProvider with list of non unassignable system attributes
     *
     * @return array
     */
    public function nonUnassignableSystemAttributesDataProvider()
    {
        return array(
            array('allow_open_amount'),
            array('category_ids'),
            array('description'),
            array('gift_message_available'),
            array('giftcard_amounts'),
            array('group_price'),
            array('image'),
            array('name'),
            array('price'),
            array('price_view'),
            array('quantity_and_stock_status'),
            array('short_description'),
            array('sku'),
            array('status'),
            array('tax_class_id'),
            array('tier_price'),
            array('visibility'),
            array('weight'),
        );
    }
}
