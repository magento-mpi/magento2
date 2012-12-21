<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'name' => 'Category Test On Store' . uniqid(),
    'is_active' => 0,
    'landing_page' => 2, // ID of CMS block
    'description' => 'some description on store',
    'default_sort_by' => 'price',
    'available_sort_by' => array('position', 'price'),
    'display_mode' => Mage_Catalog_Model_Category::DM_PAGE,
    'include_in_menu' => 0,
    'page_layout' => 'two_columns_left',
    'custom_design' => 'default/modern/default',
    'custom_design_from' => date('Y-m-d'), // date of start use design
    'custom_design_to' => date('Y-m-d', time() + 24*3600), // date of finish use design
    'custom_layout_update' => '<block type="core/text_list" name="content_on_store" output="toHtml"/>',
    'meta_description' => 'Meta description on store',
    'meta_keywords' => 'Meta keywords on store',
    'meta_title' => 'Meta title on store',
    'url_key' => 'url-key-on-store' . uniqid(),
);
