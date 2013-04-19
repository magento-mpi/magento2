<?php

$schema = array(
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
);
