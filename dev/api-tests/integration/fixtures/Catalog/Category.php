<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$category = new Mage_Catalog_Model_Category();
$category->setData(array(
    'name' => 'Category Test' . uniqid(),
    'is_active' => 1,
    'is_anchor' => 1,
    'landing_page' => 1, // ID of CMS block
    'position' => 100,
    'description' => 'some description',
    'default_sort_by' => 'name',
    'available_sort_by' => array('name'),
    'display_mode' => Mage_Catalog_Model_Category::DM_PRODUCT,
    'landing_page' => 1, // ID of static block
    'include_in_menu' => 1,
    'page_layout' => 'one_column',
    'custom_design' => 'default/default',
    'custom_design_apply' => 'someValue', // deprecated attribute, should be empty
    'custom_design_from' => '11/16/2011', // date of start use design
    'custom_design_to' => '11/21/2011', // date of finish use design
    'custom_layout_update' => '<block type="core/text_list" name="content" output="toHtml"/>',
    'meta_description' => 'Meta description',
    'meta_keywords' => 'Meta keywords',
    'meta_title' => 'Meta title',
    'url_key' => 'url-key' . uniqid(),
));
$parentCategory = Mage::getModel('Mage_Catalog_Model_Category')->load(Mage_Catalog_Model_Category::TREE_ROOT_ID);
$category->setPath($parentCategory->getPath());
$category->setStoreId(0);
return $category;
