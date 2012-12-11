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
 * Check the impossibility to edit Apply to values for system attributes
 */
class Core_Mage_ProductAttribute_SystemAttributeTest extends Mage_Selenium_TestCase
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
     * <p>Values of Apply To dropdown and multiselect are defined and can't be changed for all system attributes</p>
     *
     * @param string $attributeCode
     * @param string $applyTo
     * @param array $types
     *
     * @test
     * @dataProvider systemAttributesDataProvider
     * @TestLinkId TL-MAGE-6423
     */
    public function checkApplyProductTypeOptionDisabled($attributeCode, $applyTo, $types)
    {
        //Steps
        $this->addParameter('attribute_code', $attributeCode);
        switch ($attributeCode) {
            case 'price':
                $this->searchAndOpen(array('attribute_code'=> 'price',
                                           'used_in_layered_navigation' => 'Filterable (with results)'),
                                     'attributes_grid');
                break;
            case 'status':
                $this->searchAndOpen(array('attribute_label' => 'Status'), 'attributes_grid');
                break;
            default:
                $this->searchAndOpen(array('attribute_code' => $attributeCode), 'attributes_grid');
                break;
        }
        //Verifying
        $dropdownXpath = $this->_getControlXpath('dropdown', 'apply_to');
        $this->assertTrue($this->elementIsPresent($dropdownXpath . "[@disabled = 'disabled']"));
        if ($applyTo == 'All Product Types') {
            $this->assertFalse($this->elementIsPresent('apply_product_types'));
        }
        else {
            $this->assertTrue($this->controlIsPresent('multiselect', 'apply_product_types'),
                'Apply To multiselect is absent');
            $this->assertFalse($this->controlIsEditable('multiselect', 'apply_product_types'),
                'Apply To multiselect is enabled');
            $selected = (array)($this->getControlAttribute('multiselect', 'apply_product_types', 'selectedValue'));
            $this->assertEmpty(array_diff($selected, $types));
        }
    }

    /**
     * <p>Data Provider with system attributes list and defined product types' applying</p>
     *
     * @return array
     */
    public function systemAttributesDataProvider()
    {
        return array(
            array('category_ids', 'All Product Types', null),
            array('country_of_manufacture', 'Selected Product Types',
                array('simple', 'grouped', 'configurable', 'bundle')),
            array('custom_design', 'All Product Types', null),
            array('custom_design_from', 'All Product Types', null),
            array('custom_design_to', 'All Product Types', null),
            array('custom_layout_update', 'All Product Types', null),
            array('description', 'All Product Types', null),
            array('enable_googlecheckout', 'All Product Types', null),
            array('gallery', 'All Product Types', null),
            array('gift_message_available', 'All Product Types', null),
            array('group_price', 'Selected Product Types',
                array('simple', 'configurable', 'virtual', 'bundle', 'downloadable')),
            array('image', 'All Product Types', null),
            array('is_recurring', 'Selected Product Types',
                array('simple', 'virtual')),
            array('media_gallery', 'All Product Types', null),
            array('meta_description', 'All Product Types', null),
            array('meta_keyword', 'All Product Types', null),
            array('meta_title', 'All Product Types', null),
            array('msrp', 'Selected Product Types',
                array('simple', 'configurable', 'virtual', 'bundle', 'downloadable')),
            array('msrp_display_actual_price_type', 'Selected Product Types',
                array('simple', 'configurable', 'virtual', 'bundle', 'downloadable')),
            array('msrp_enabled', 'Selected Product Types',
                array('simple', 'configurable', 'virtual', 'bundle', 'downloadable')),
            array('name', 'All Product Types', null),
            array('news_from_date', 'All Product Types', null),
            array('news_to_date', 'All Product Types', null),
            array('options_container', 'All Product Types', null),
            array('page_layout', 'All Product Types', null),
            array('price', 'Selected Product Types',
                array('simple', 'configurable', 'virtual', 'bundle', 'downloadable')),
            array('price_view', 'Selected Product Types',
                array('bundle')),
            array('quantity_and_stock_status', 'All Product Types', null),
            array('recurring_profile', 'Selected Product Types',
                array('simple', 'virtual')),
            array('short_description', 'All Product Types', null),
            array('sku', 'All Product Types', null),
            array ('small_image', 'All Product Types', null),
            array('special_from_date', 'Selected Product Types',
                array('simple', 'configurable', 'virtual', 'bundle', 'downloadable')),
            array('special_price', 'Selected Product Types',
                array('simple', 'configurable', 'virtual', 'bundle', 'downloadable')),
            array('special_to_date', 'Selected Product Types',
                array('simple', 'configurable', 'virtual', 'bundle', 'downloadable')),
            array('status', 'All Product Types', null),
            array('tax_class_id', 'Selected Product Types',
                array('simple', 'configurable', 'virtual', 'bundle', 'downloadable')),
            array('thumbnail', 'All Product Types', null),
            array('tier_price', 'Selected Product Types',
                array('simple', 'configurable', 'virtual', 'bundle', 'downloadable')),
            array('url_key', 'All Product Types', null),
            array('visibility', 'All Product Types', null),
            array('weight', 'Selected Product Types',
                array('simple', 'virtual', 'bundle', 'downloadable')),
        );
    }
}
