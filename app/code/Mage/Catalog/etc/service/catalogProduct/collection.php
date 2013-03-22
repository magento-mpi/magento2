<?php

$resourceDefinition = array(
    'params' => array(
        'entity_ids' => array(
            'label'      => 'IDs',
            'comment'    => '',
            'type'       => 'number',
            'input_type' => 'multiselect'
        ),
        'skus'       => array(
            'label'      => 'SKUs',
            'comment'    => '',
            'type'       => 'text',
            'input_type' => 'multiselect'
        ),
        'start'      => array(
            'label'      => 'Pagination: Start position',
            'comment'    => '',
            'type'       => 'number',
            'input_type' => 'text'
        ),
        'limit'      => array(
            'label'      => 'Pagination: Limit',
            'comment'    => '',
            'type'       => 'number',
            'input_type' => 'text'
        ),
        'sort_order' => array(
            'label'      => '"Sort by" field name',
            'comment'    => '',
            'type'       => 'number',
            'input_type' => 'text'
        )
    ),
    'return' => array(
        'totalCount' => array(
            'label'      => 'Collection Items Count',
            'comment'    => '',
            'type'       => 'number',
            'input_type' => 'text'
        ),
        'items'      => array(
            array('_resource' => 'catalogProduct', '_unbound' => true)
        )
    )
);
