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
class Saas_Mage_AttributeSet_UnassignSystemAttributeTest extends Core_Mage_AttributeSet_UnassignSystemAttributeTest
{
    /**
     * DataProvider for system attributes, which can be unassigned
     * Override to exclude 'is_recurring', 'recurring_profile'
     *
     * @return array
     */
    public function unassignableSystemAttributesDataProvider()
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
            array('media_gallery'),
            array('meta_description'),
            array('meta_keyword'),
            array('meta_title'),
            array('msrp'),
            array('msrp_display_actual_price_type'),
            array('msrp_enabled'),
            array('news_from_date'),
            array('news_to_date'),
            array('options_container'),
            array('page_layout'),
            array('small_image'),
            array('special_from_date'),
            array('special_price'),
            array('special_to_date'),
            array('thumbnail'),
            array('url_key')
        );
    }
}
