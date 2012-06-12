<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


return array(
    'create'    => array(
        'type'  => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
        'set'   => 4,
        'sku'   => 'simple' . uniqid(),
        'productData'   => array(
            'name' => 'test',
            'description' => 'description',
            'short_description' => 'short description',
            'weight' => 1,
            'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
            'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            'price' => 9.99,
            'tax_class_id' => 2,
        )
    ),
    'update'   => array(
        'productData'   => array(
            'status'        => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,   //required to see product on backend
            'name'          => 'Simple Product Updated',    //test update method
        )
    ),
    'update_custom_store'   => array(
        'productData'   => array(
            'status'        => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,   //required to see product on backend
            'name'          => 'Simple Product Updated Custom Store',    //test update method
        ),
        'store' => 'test_store'
    ),
    'update_default_store' => array(
        'productData' => array(
            'description' => 'Updated description'
        )
    ),
    'create_with_attributes_soapv2'    => array(
        'type'  => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
        'set'   => 4,
        'sku'   => 'simple' . uniqid(),
        'productData'   => array(
            'status'        => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,   //required to see product on backend
            'name'          => 'Product with attributes',
            'description' => 'description',
            'short_description' => 'short description',
            'weight' => 1,
            'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            'price' => 9.99,
            'tax_class_id' => 2,
            'additional_attributes' => array(
                'single_data'     => array(
                    array(
                        'key' => 'a_text_api',
                        'value' => 'qqq123'
                    ),
                    array(
                        'key' => 'a_select_api',
                        'value' => '__PLACEHOLDER__'
                    ),
                    array(
                        'key' => 'a_text_ins',
                        'value' => 'qqq123'
                    ),
                    array(
                        'key' => 'a_select_ins',
                        'value' => '__PLACEHOLDER__'
                    ),
                ),
                'multi_data' => array()
            )
        )
    ),
    'create_with_attributes_soap'    => array(
        'type'  => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
        'set'   => 4,
        'sku'   => 'simple' . uniqid(),
        'productData'   => array(
            'status'        => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,   //required to see product on backend
            'name'          => 'Product with attributes',
            'description' => 'description',
            'short_description' => 'short description',
            'weight' => 1,
            'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            'price' => 9.99,
            'tax_class_id' => 2,
            'additional_attributes' => array(
                'single_data'     => array(
                    'a_text_api'    => 'qqq123',
                    'a_select_api'  => '__PLACEHOLDER__',
                    'a_text_ins'    => 'qqq123',
                    'a_select_ins'  => '__PLACEHOLDER__'
                ),
                'multi_data' => array()
            )
        )
    ),
    'create_full_fledged' => array(
        'sku'               => 'simple' . uniqid(),
        'attribute_set_id'  => 4,
        'type_id'           => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
        'name'              => 'Simple Product',
        'website_ids'       => array(Mage::app()->getStore()->getWebsiteId()),
        'description'       => '...',
        'short_description' => '...',
        'price'             => 0.99,
        'tax_class_id'      => 2,
        'visibility'        => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
        'status'            => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
        'special_from_date' => false // to avoid set this attr to '' which leads to unpredictable bugs
    ),
    'create_full' => array(
        'soap' => array(
            'type' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
            'set'  => 4,
            'sku'  => 'simple' . uniqid(),
            'productData' => array(
                'name'              => 'Simple Product',
                'website_ids'       => array(Mage::app()->getStore()->getWebsiteId()),
                'description'       => '...',
                'short_description' => '...',
                'price'             => 0.99,
                'tax_class_id'      => 2,
                'visibility'        => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                'status'            => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
                'weight'            => 1,
            )
        )
    )
);
