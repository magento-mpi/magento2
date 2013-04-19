<?php

$schema = array(
    'fields' => array(
        'store_id'       => array(
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
        'entity_id'      => array(
            'label'       => 'Entity ID',
            'type'        => 'integer',
            'required'    => true,
            'default'     => null,
            'constraints' => array(
                'is_integer' => array(
                    'class' => 'Magento_Validator_Int'
                )
            )
        )
    ),

    'data_namespace' => 'catalog_category'
);
