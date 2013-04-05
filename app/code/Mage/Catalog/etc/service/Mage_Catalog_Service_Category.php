<?php

$resourceDefinition = array(
    'methods' => array(
        'item' => array(
            'args'           => array(
                'entity_id' => array(),

                'url_key'   => array(),

                'store_id'  => array(
                    'default' => null
                ),

                'version'   => array(
                    'default' => null
                ),

                'fields'    => array()
            ),
            'id_field_alias' => 'category_id',
            'return'         => array(
                array('_resource' => 'catalogCategory')
            )
        )
    )
);
