<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
$parentCategory = Mage::getModel('catalog/category')->load(Mage_Catalog_Model_Category::TREE_ROOT_ID);
$category->setPath($parentCategory->getPath());
$category->setStoreId(0);
return $category;
