<?php

$schema = array(
    'constraints' => array(
        'eav_validator' => array(
            'class' => 'Mage_Eav_Model_Validator_Attribute_Data'
        )
    ),
    'fields'      => array(
        'store_id'  => array(
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
        'fields'    => array(
            'label'        => 'Partial Response Fields List',
            'type'         => 'mixed',
            'required'     => false,
            'default'      => null,
            'content_type' => 'list'
        ),
        'filters'    => array(
            'label'        => 'Filters',
            'type'         => 'mixed',
            'required'     => false,
            'default'      => null,
            'content_type' => 'json'
        )
    )
);
