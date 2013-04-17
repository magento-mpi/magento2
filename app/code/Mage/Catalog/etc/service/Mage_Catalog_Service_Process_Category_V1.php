<?php

$resourceDefinition = array(
    'request_schema'  => array(
        'view'    => array(
            'entity_id'       => array(
                'label'       => 'Entity ID',
                'type'        => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'input_type'  => 'label',
                'size'        => null,
                'identity'    => true,
                'nullable'    => false,
                'primary'     => true,
                '_constraint' => array(
                    'class' => 'Magento_Validator_Int'
                )
            ),
            'store_id'        => array(
                'default'     => null,
                'required'    => false,
                '_constraint' => array(
                    'class' => 'Magento_Validator_Int'
                )
            ),

            'controller_action'     => array(
                'label'       => 'Controller Action Instance',
                'type'        => 'object'
            ),
            'response'              => array(
                'label'       => 'Container for response',
                'type'        => 'object'
            ),
            'current_area'          => array(
                'label'       => 'Layout Area ID',
                'type'        => 'string'
            ),
            'default_layout_handle' => array(
                'label'       => 'Default Layout Handle Name',
                'type'        => 'string'
            ),

            'data_namespace'  => 'catalog_category',
            'response_schema' => array()
        )
    ),
    'response_schema' => array(
        //
    )
);
