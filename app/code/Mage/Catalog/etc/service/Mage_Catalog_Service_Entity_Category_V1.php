<?php

$resourceDefinition = array(
    'request_schema'  => array(
        '*'    => array( // `*` - defines default service-level schema
            '_ref'            => 'entity/catalog_category',

            // probably we may refer to entity fields by alias: "core_store/store_id" and define only differences
            // from original declarations:
            //'core_store/store_id' => array(
            //    'required' => false,
            //),
            'store_id'        => array(
                'default'     => null,
                'required'    => false,
                '_constraint' => array(
                    'class' => 'Magento_Validator_Int'
                )
            ),

            // BEGIN: EXCERPTED FROM ORIGINAL ENTITY DEFINITION
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
            // END: EXCERPTED FROM ORIGINAL ENTITY DEFINITION

            'data_namespace'  => 'catalog_category',
            'response_schema' => array()
        ),
        'save' => array(
            '_constraint' => array(
                'class' => 'Mage_Eav_Model_Validator_Attribute_Data'
            )
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
            'is_active' => array()
        )
    )
);
