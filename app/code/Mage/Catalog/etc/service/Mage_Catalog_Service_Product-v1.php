<?php

$resourceDefinition = array(
    'request_schema'  => array(
        // having all schemas defined on the same level will let us to share schemas between methods
        '*' => array( // `*` - defines default service-level schema
            '_ref'             => 'entity/catalog_product',

            // BEGIN: EXCERPTED FROM ORIGINAL DEFINITION
            'product_id'       => array(
                'label'      => 'Entity ID',
                'type'       => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'input_type' => 'label',
                'size'       => null,
                'identity'   => true,
                'nullable'   => false,
                'primary'    => true,
            ),
            'attribute_set_id' => array(
                'default' => null
            ),
            'type_id'          => array(
                'default' => null
            ),
            // END: EXCERPTED FROM ORIGINAL DEFINITION

            'store_id'         => array(
                'default' => null
            ),
            'data_namespace'   => 'catalog_product',
        )
    ),
    'response_schema' => array(
        '*' => array( // `*` - defines default service-level schema
            '_ref'             => 'entity/catalog_product',
            'name'             => array(
                'required' => true
            ),
            'prices'           => array(
                '_elements' => array(
                    'price'       => array(),
                    'tier_prices' => array(
                        'get_callback' => array(
                            'Mage_Catalog_Model_Product_Price',
                            'getTierPrices'
                        ),
                        'set_callback' => array(
                            'Mage_Catalog_Model_Product_Price',
                            'setTierPrices'
                        )
                    )
                )
            ),
            'media_gallery'    => array(
                'get_callback' => array(
                    'Mage_Catalog_Model_Product_Gallery',
                    'getData'
                ),
                'set_callback' => array(
                    'Mage_Catalog_Model_Product_Gallery',
                    'setData'
                )
            ),
            'related_entities' => array(
                '_elements' => array(
                    'crosssells' => array(
                        'get_callback' => array(
                            'Mage_Catalog_Model_Product',
                            'getCrossSellProductCollection'
                        ),
                        'set_callback' => array(
                            'Mage_Catalog_Model_Product',
                            'setCrossSellProducts'
                        )
                    ),
                    'upsells'    => array(
                        'get_callback' => array(
                            'Mage_Catalog_Model_Product',
                            'getUpSellProductCollection'
                        ),
                        'set_callback' => array(
                            'Mage_Catalog_Model_Product',
                            'setUpSellProducts'
                        )
                    )
                )
            )
        )
    )
);
