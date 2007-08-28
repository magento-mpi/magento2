<?php

$this->addAttribute('catalog_category', 'page_layout', array(
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
));