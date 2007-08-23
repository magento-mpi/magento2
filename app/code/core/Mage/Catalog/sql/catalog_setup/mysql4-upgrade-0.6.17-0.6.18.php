<?php

$this->addAttribute('catalog_product', 'thumbnail', array(
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
));