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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog entity setup
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
            'catalog_category'=>array(
                'table'=>'catalog/category',
                'is_data_sharing' => false,
                'attributes' => array(
                    'name'          => array(
                        'type'      => 'varchar',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Name',
                        'input'     => 'text',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'description'   => array(
                        'type'      => 'text',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Description',
                        'input'     => 'textarea',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'image'         => array(
                        'type'      => 'varchar',
                        'backend'   => 'catalog_entity/category_attribute_backend_image',
                        'frontend'  => 'catalog_entity/category_attribute_frontend_image',
                        'label'     => 'Image',
                        'input'     => 'image',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'meta_title'    => array(
                        'type'      => 'varchar',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Page Title',
                        'input'     => 'text',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'meta_keywords' => array(
                        'type'      => 'text',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Meta Keywords',
                        'input'     => 'textarea',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'meta_description'=> array(
                        'type'      => 'text',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Meta Description',
                        'input'     => 'textarea',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'display_mode'  => array(
                        'type'      => 'varchar',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Display Mode',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => 'catalog_entity/category_attribute_source_mode',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'landing_page'  => array(
                        'type'      => 'int',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'CMS Block',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => 'catalog_entity/category_attribute_source_page',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'page_layout'=>array(
                        'type'      => 'varchar',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Page Layout',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => 'catalog_entity/category_attribute_source_layout',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'is_anchor'     => array(
                        'type'      => 'int',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Is Anchor',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => 'eav/entity_attribute_source_boolean',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'is_active'     => array(
                        'type'      => 'static',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Is Active',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => 'eav/entity_attribute_source_boolean',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'all_children'  => array(
                        'type'      => 'text',
                        'backend'   => 'catalog_entity/category_attribute_backend_tree_children',
                        'frontend'  => '',
                        'label'     => '',
                        'input'     => '',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => false,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'path_in_store' => array(
                        'type'      => 'text',
                        'backend'   => 'catalog_entity/category_attribute_backend_tree_path',
                        'frontend'  => '',
                        'table'     => '',
                        'label'     => '',
                        'input'     => '',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => false,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'children'      => array(
                        'type'      => 'text',
                        'backend'   => 'catalog_entity/category_attribute_backend_tree_children',
                        'frontend'  => '',
                        'table'     => '',
                        'label'     => '',
                        'input'     => '',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => false,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'url_key' => array(
                        'label'     => 'URL key',
                        'backend'   => 'catalog_entity/category_attribute_backend_urlkey',
                        'required'  => false,
                    ),
                    'url_path' => array(
                        'type'      => 'varchar',
                        'label'     => '',
                        'frontend'  => '',
                        'table'     => '',
                        'label'     => '',
                        'input'     => '',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => false,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => true,
                    ),
                    'custom_layout_update' => array(
                        'type'      => 'text',
                        'label'     => 'Custom Layout Update',
                        'frontend'  => '',
                        'table'     => '',
                        'label'     => '',
                        'input'     => 'textarea',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                ),
            ),
            'catalog_product' => array(
                'table'=>'catalog/product',
                'is_data_sharing' => false,
                'attributes' => array(
                    'name' => array(
                        'type'      => 'varchar',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Name',
                        'input'     => 'text',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> true,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'description' => array(
                        'type'      => 'text',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Description',
                        'input'     => 'textarea',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> true,
                        'filterable'=> false,
                        'comparable'=> true,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'short_description' => array(
                        'type'      => 'text',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Short Description',
                        'input'     => 'textarea',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> true,
                        'filterable'=> false,
                        'comparable'=> true,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'sku' => array(
                        'type'      => 'static',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'SKU',
                        'input'     => 'text',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> true,
                        'filterable'=> false,
                        'comparable'=> true,
                        'visible_on_front' => false,
                        'unique'    => true,
                    ),
                    'price' => array(
                        'group'     => 'Prices',
                        'type'      => 'decimal',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Price',
                        'input'     => 'price',
                        'class'     => 'validate-number',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> true,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'cost' => array(
                        'group'     => 'Prices',
                        'type'      => 'decimal',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Cost',
                        'input'     => 'price',
                        'class'     => 'validate-number',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => true,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'weight' => array(
                        'type'      => 'decimal',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Weight',
                        'input'     => 'text',
                        'class'     => 'validate-number',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                        'use_in_super_product' => false,
                    ),
                    'manufacturer' => array(
                        'type'      => 'int',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Manufacturer',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => true,
                        'default'   => '',
                        'searchable'=> true,
                        'filterable'=> true,
                        'comparable'=> true,
                        'visible_on_front' => false,
                        'unique'    => false,
                        'use_in_super_product' => false,
                    ),
                    'meta_title' => array(
                        'group'     => 'Meta Information',
                        'type'      => 'varchar',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Meta Title',
                        'input'     => 'text',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'meta_keyword' => array(
                        'group'     => 'Meta Information',
                        'type'      => 'text',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Meta Keywords',
                        'input'     => 'textarea',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'meta_description' => array(
                        'group'     => 'Meta Information',
                        'type'      => 'varchar',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Meta Description',
                        'input'     => 'textarea',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'image' => array(
                        'group'     => 'Images',
                        'type'      => 'varchar',
                        'backend'   => 'catalog_entity/product_attribute_backend_image',
                        'frontend'  => 'catalog_entity/product_attribute_frontend_image',
                        'label'     => 'Base Image',
                        'input'     => 'image',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'small_image' => array(
                        'group'     => 'Images',
                        'type'      => 'varchar',
                        'backend'   => 'catalog_entity/product_attribute_backend_image',
                        'frontend'  => 'catalog_entity/product_attribute_frontend_image',
                        'label'     => 'Small Image',
                        'input'     => 'image',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'thumbnail' => array(
                        'group'     => 'Images',
                        'type'      => 'varchar',
                        'backend'   => 'catalog_entity/product_attribute_backend_image',
                        'frontend'  => 'catalog_entity/product_attribute_frontend_image',
                        'label'     => 'Thumbnail',
                        'input'     => 'image',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'old_id' => array(
                        'type'      => 'int',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => '',
                        'input'     => '',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => false,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'tier_price' => array(
                        'group'     => 'Prices',
                        'type'      => 'decimal',
                        'backend'   => 'catalog/entity_product_attribute_backend_tierprice',
                        'frontend'  => 'catalog/entity_product_attribute_frontend_tierprice',
                        'label'     => 'Tier Price',
                        'input'     => 'text',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'color'          => array(
                        'type'      => 'int',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Color',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => true,
                        'default'   => '',
                        'searchable'=> true,
                        'filterable'=> true,
                        'comparable'=> true,
                        'visible_on_front' => false,
                        'unique'    => false,
                        'use_in_super_product' => false,
                    ),
                    'gallery' => array(
                        'group'     => 'Images',
                        'type'      => 'varchar',
                        'backend'   => 'catalog_entity/product_attribute_backend_gallery',
                        'table'     => 'catalog_product_entity_gallery',
                        'frontend'  => '',
                        'label'     => 'Image Gallery',
                        'input'     => 'gallery',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'status' => array(
                        'type'      => 'int',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Status',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => 'catalog/entity_product_attribute_source_status',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> true,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'tax_class_id' => array(
                        'type'      => 'int',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Tax Class',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => 'tax/class_source_product',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> true,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'url_key' => array(
                        'label'     => 'URL key',
                        'backend'   => 'catalog_entity/product_attribute_backend_urlkey',
                        'required'  => false,
                    ),
                    'url_path' => array(
                        'type'      => 'varchar',
                        'label'     => '',
                        'frontend'  => '',
                        'table'     => '',
                        'label'     => '',
                        'input'     => '',
                        'class'     => '',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => false,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => true,
                    ),
                    'minimal_price' => array(
                        'group'     => 'Prices',
                        'type'      => 'decimal',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Minimal Price',
                        'input'     => 'price',
                        'class'     => 'validate-number',
                        'source'    => '',
                        'global'    => false,
                        'visible'   => false,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'visibility' => array(
                        'group'     => 'General',
                        'type'      => 'int',
                        'backend'   => '',
                        'frontend'  => '',
                        'label'     => 'Visibility',
                        'input'     => 'select',
                        'class'     => '',
                        'source'    => 'catalog/entity_product_attribute_source_visibility',
                        'global'    => false,
                        'visible'   => true,
                        'required'  => true,
                        'user_defined' => false,
                        'default'   => '3',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
                    'custom_layout_update' => array(
                        'type'      => 'text',
                        'label'     => 'Custom Layout Update',
                        'frontend'  => '',
                        'table'     => '',
                        'label'     => '',
                        'input'     => 'textarea',
                        'class'     => '',
                        'source'    => '',
                        'global'    => true,
                        'visible'   => true,
                        'required'  => false,
                        'user_defined' => false,
                        'default'   => '',
                        'searchable'=> false,
                        'filterable'=> false,
                        'comparable'=> false,
                        'visible_on_front' => false,
                        'unique'    => false,
                    ),
               ),
            ),
        );
    }
}
