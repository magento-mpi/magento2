<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'entities' => array(
        'product' => array(
            'name' => 'product',
            'label' => 'Label_One',
            'model' => 'Model_One',
            'types' => array(
                'product_type_one' => array('name' => 'product_type_one', 'model' => 'Product_Model_Type_One'),
                'type_two' => array('name' => 'type_two', 'model' => 'Model_Type_Two')
            ),
            'entityAttributeFilterType' => 'product'
        ),
        'customer' => array(
            'name' => 'customer',
            'label' => 'Label_One',
            'model' => 'Model_One',
            'types' => array(
                'type_one' => array('name' => 'type_one', 'model' => 'Model_Type_One'),
                'type_two' => array('name' => 'type_two', 'model' => 'Model_Type_Two')
            ),
            'entityAttributeFilterType' => 'customer'
        )
    ),
    'fileFormats' => array(
        'name_three' => array('name' => 'name_three', 'model' => 'Model_Three', 'label' => 'Label_Three')
    )
);
