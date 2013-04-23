<?php

$schema = array(
    'fields' => array(
        'entity_id'       => array(
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
    )
);
