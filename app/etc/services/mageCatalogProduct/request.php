<?php

$schema = array(
    '_ref'           => 'entity/catalog_product',
    'fields'         => array(
        'store_id'         => array(
            'label'       => 'Store ID',
            'type'        => 'integer',
            'required'    => false,
            'default'     => null,
            'constraints' => array(
                'is_integer' => array(
                    'class' => 'Magento_Validator_Int'
                )
            )
        ),
        'entity_id'        => array(
            'label'       => 'Entity ID',
            'type'        => 'integer',
            'required'    => true,
            'default'     => null,
            'constraints' => array(
                'is_integer' => array(
                    'class' => 'Magento_Validator_Int'
                )
            )
        ),
        'name'        => array(
            'label'       => 'Product Name',
            'type'        => 'string',
            'required'    => false,
            'default'     => null
        ),
        'sku'        => array(
            'label'       => 'Product SKU',
            'type'        => 'string',
            'required'    => false,
            'default'     => null
        ),
        'attribute_set_id' => array(
            'label'       => 'Attribute Set ID',
            'default'     => null,
            '_constraint' => array(
                'class' => 'Magento_Validator_Int'
            )
        ),
        'type_id'          => array(
            'label'       => 'Product Type ID',
            'default'     => null,
            '_constraint' => array(
                'class' => 'Magento_Validator_Int'
            )
        ),
        'fields'    => array(
            'label'        => 'Partial Response Fields List',
            'type'         => 'mixed',
            'required'     => false,
            'default'      => null,
            'content_type' => 'list'
        )
    ),
    'data_namespace' => 'catalog_product',
);
