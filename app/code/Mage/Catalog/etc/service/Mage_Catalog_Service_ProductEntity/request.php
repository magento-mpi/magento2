<?php

$schema = array(
    '_ref'             => 'entity/catalog_product',

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

    'data_namespace'   => 'catalog_product',
);
