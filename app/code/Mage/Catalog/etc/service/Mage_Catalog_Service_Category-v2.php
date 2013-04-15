<?php

$resourceDefinition = array(
    'request_schema'  => array(
        '*' => array( // `*` - defines default service-level schema
            '_ref'            => 'entity/catalog_category',

            // BEGIN: EXCERPTED FROM ORIGINAL DEFINITION
            'entity_id'       => array(
                'label'      => 'Entity ID',
                'type'       => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'input_type' => 'label',
                'size'       => null,
                'identity'   => true,
                'nullable'   => false,
                'primary'    => true,
            ),
            // END: EXCERPTED FROM ORIGINAL DEFINITION

            'store_id'        => array(
                'default'  => null,
                'required' => true // ! incompatible change ! new version has been introduced
            ),

            'data_namespace'  => 'catalog_category',
            'response_schema' => array()
        )
    ),
    'response_schema' => array(
        '*'    => array( // `*` - defines default service-level schema
            '_ref'      => 'entity/catalog_category',

            'entity_id' => array(),
            'name'      => array()
        ),
        'item' => array( // defines method-specific schema
            '_ref'      => 'entity/catalog_category',

            'entity_id' => array(),
            'name'      => array(),
            'is_active' => array(),

            'path'      => array(),
            'url_key'   => array(),
            'url_path'  => array()
        )
    )
);
