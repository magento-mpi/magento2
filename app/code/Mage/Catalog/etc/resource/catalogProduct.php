<?php

$resourceDefinition = array(
    'fields' => array(
        'entity_id'   => array(
            'type'                       => 'static',
            'label'                      => 'Product Entity ID',
            'input'                      => 'int',
            'backend'                    => '',
            'primary'                    => true,
            'autoincrement'              => true,
            'unique'                     => true,
            'sort_order'                 => 1,
            'filterable'                 => true,
            'searchable'                 => true,
            'comparable'                 => false,
            'visible_in_advanced_search' => false
        ),

        'sku'         => array(
            'type'                       => 'static',
            'label'                      => 'SKU',
            'input'                      => 'text',
            'backend'                    => 'Mage_Catalog_Model_Product_Attribute_Backend_Sku',
            'unique'                     => true,
            'sort_order'                 => 2,
            'filterable'                 => true,
            'searchable'                 => true,
            'comparable'                 => true,
            'visible_in_advanced_search' => true
        ),

        'name'        => array(
            'type'                       => 'varchar',
            'label'                      => 'Name',
            'input'                      => 'text',
            'sort_order'                 => 3,
            'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'filterable'                 => true,
            'searchable'                 => true,
            'comparable'                 => false,
            'visible_in_advanced_search' => true,
            'used_in_product_listing'    => true,
            'used_for_sort_by'           => true
        ),

        'description' => array(
            'type'                       => 'text',
            'label'                      => 'Description',
            'input'                      => 'textarea',
            'sort_order'                 => 4,
            'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'searchable'                 => true,
            'comparable'                 => true,
            'wysiwyg_enabled'            => true,
            'is_html_allowed_on_front'   => true,
            'visible_in_advanced_search' => true
        )
    )
);
