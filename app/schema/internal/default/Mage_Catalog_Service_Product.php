<?php

$schema = array(
    'methods' => array(
        'item' => array(
            'request'  => array(
                'data_namespace' => 'catalog_product',
                'fields'         => array(
                    'store_id'         => array(
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
                    'entity_id'        => array(
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
                    'attribute_set_id' => array(
                        'label'       => 'Attribute Set ID',
                        'default'     => null,
                        '_constraint' => array(
                            'class' => 'Magento_Validator_Int'
                        )
                    ),
                    'type_id'          => array(
                        'label'       => 'Product Type ID',
                        'default'     => null,
                        '_constraint' => array(
                            'class' => 'Magento_Validator_Int'
                        )
                    ),
                    'fields'           => array(
                        'label'        => 'Partial Response Fields List',
                        'type'         => 'mixed',
                        'required'     => false,
                        'default'      => null,
                        'content_type' => 'list'
                    )
                )
            ),
            'response' => array(
                'fields' => array(
                    'entity_id'                      => array(
                        'required' => true
                    ),
                    'name'                           => array(
                        'required' => true
                    ),
                    'sku'                            => array(
                        'required' => true
                    ),
                    'description'                    => array(
                        'required' => true
                    ),
                    'short_description'              => array(
                        'required' => true
                    ),

                    'weight'                         => array(
                        'required' => true
                    ),
                    'manufacturer'                   => array(
                        'required' => false,
                        'field'    => 'brand'
                    ),
                    'meta_title'                     => array(
                        'required' => false
                    ),
                    'meta_keyword'                   => array(
                        'required' => false
                    ),
                    'meta_description'               => array(
                        'required' => false
                    ),
                    'media_gallery'                  => array(
                        'required' => false
                    ),
                    'old_id'                         => array(
                        'required' => false
                    ),
                    'color'                          => array(
                        'required' => false
                    ),
                    'news_from_date'                 => array(
                        'required' => false
                    ),
                    'news_to_date'                   => array(
                        'required' => false
                    ),
                    'gallery'                        => array(
                        'required' => false
                    ),
                    'status'                         => array(
                        'required' => true
                    ),
                    'url_key'                        => array(
                        'required' => false
                    ),
                    'url_path'                       => array(
                        'required' => false
                    ),

                    'is_recurring'                   => array(
                        'required' => false
                    ),
                    'recurring_profile'              => array(
                        'required' => false
                    ),
                    'visibility'                     => array(
                        'required' => true
                    ),
                    'custom_design'                  => array(
                        'required' => false
                    ),
                    'customD_dsign_from'             => array(
                        'required' => false
                    ),
                    'custom_design_to'               => array(
                        'required' => false
                    ),
                    'custom_layout_update'           => array(
                        'required' => false
                    ),
                    'page_layout'                    => array(
                        'required' => false
                    ),
                    'category_ids'                   => array(
                        'required' => false
                    ),
                    'has_options'                    => array(
                        'required' => false
                    ),
                    'required_options'               => array(
                        'required' => false
                    ),
                    'created_at'                     => array(
                        'required' => true
                    ),
                    'updated_at'                     => array(
                        'required' => true
                    ),
                    'country_of_manufacture'         => array(
                        'required' => false
                    ),
                    'msrp_enabled'                   => array(
                        'required' => false
                    ),
                    'msrp_display_actual_price_type' => array(
                        'required' => false
                    ),
                    'msrp'                           => array(
                        'required' => false
                    ),
                    'bundle'                         => array(
                        'required'  => false,
                        'condition' => array('type_id', 'bundle'),
                        '_elements' => array(
                            'price_type'    => array(),
                            'sku_type'      => array(),
                            'weight_type'   => array(),
                            'shipment_type' => array(),
                            'price_view'    => array()
                        )
                    ),
                    'downloadable'                   => array(
                        'required'  => false,
                        '_elements' => array(
                            'links_purchased_separately' => array(),
                            'samples_title'              => array(),
                            'links_title'                => array(),
                            'shipment_type'              => array(),
                        )
                    ),
                    'gift_message_available'         => array(
                        'required' => false
                    ),
                    'tax_class_id'                   => array(
                        'required' => true
                    ),
                    'enableGoogleCheckout'           => array(
                        'required' => false
                    ),
                    'giftcard'                       => array(
                        'required'  => false,
                        '_elements' => array(
                            'giftcard_amounts'          => array(), // @todo used to be different format
                            'allow_open_amount'         => array(),
                            'open_amount_min'           => array(),
                            'open_amount_max'           => array(),
                            'giftcard_type'             => array(),
                            'is_redeemable'             => array(),
                            'use_config_is_redeemable'  => array(),
                            'lifetime'                  => array(),
                            'useConfigLifetime'         => array(),
                            'email_template'            => array(),
                            'use_config_email_template' => array(),
                            'allow_message'             => array(),
                            'use_config_allow_message'  => array()
                        )
                    ),
                    'gift_wrapping_available'        => array(
                        'required' => false
                    ),
                    'gift_wrapping_price'            => array(
                        'required' => false
                    ),
                    'is_returnable'                  => array(
                        'required' => false
                    ),
                    'target_rules'                   => array(
                        'required' => false
                    ),
                    'is_in_stock'                    => array(
                        'required' => true
                    ),
                    'qty'                            => array(
                        'required'     => false,
                        'get_callback' => 'getQuantityAndStockStatus/qty'
                    ),
                    'website_ids'                    => array(
                        'required' => false
                    ),
                    'prices'                         => array(
                        '_elements' => array(
                            'price'         => array(
                                'required' => true
                            ),
                            'special_price' => array(
                                'required'  => false,
                                '_elements' => array(
                                    'special_price'     => array(),
                                    'special_from_date' => array(),
                                    'special_to_date'   => array()
                                )
                            ),
                            'cost'          => array(
                                'required' => false
                            ),
                            'group_price'   => array(
                                'required' => false
                            ),
                            'minimal_price' => array(
                                'required' => false
                            ),
                            'tier_price'    => array(
                                'required' => false
                            )
                        )
                    ),
                    'images'                         => array(
                        'required'  => false,
                        '_elements' => array(
                            'image'       => array(),
                            'small_image' => array(),
                            'thumbnail'   => array()
                        )
                    ),
                    'stock_item'                     => array( // @todo need this to have system working
                        'required' => true
                    ),
                    'can_email_to_friend'            => array(
                        'required'     => false,
                        'get_callback' => array('Mage_Catalog_Block_Product_View', 'canEmailToFriend')
                    ),
                    'has_options'                    => array(
                        'required' => false
                    ),
                    'options_container'              => array(
                        'required' => false,
                        'default'  => 'container2'
                    ),
                    'wishlist_enabled'               => array(
                        'required' => false
                    )
                )
            )
        )
    )
);
