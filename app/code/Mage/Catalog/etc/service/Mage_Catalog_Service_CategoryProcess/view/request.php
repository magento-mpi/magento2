<?php

$schema = array(
    'fields' => array(
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
        'entity_id' => array(
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

        'controller_action'     => array(
            'label' => 'Controller Action Instance',
            'type'  => 'object'
        ),
        'response'              => array(
            'label' => 'Container for response',
            'type'  => 'object'
        ),
        'current_area'          => array(
            'label' => 'Layout Area ID',
            'type'  => 'string'
        ),
        'default_layout_handle' => array(
            'label' => 'Default Layout Handle Name',
            'type'  => 'string'
        ),

        'response_schema'       => array(
            'label' => 'Response Schema',
            'type'  => 'mixed'
        )
    )
);
