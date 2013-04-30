<?php

$schema = array(
    '_ref'   => 'entity/catalog_product',
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
            'required' => false
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
        'website_ids'                    => array(
            'required' => false
        )
    )
);
