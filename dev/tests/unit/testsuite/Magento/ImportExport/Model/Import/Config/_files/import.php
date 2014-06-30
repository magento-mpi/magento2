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
            'behaviorModel' => 'Model_Basic',
            'model' => 'Model_One',
            'types' =>
                array(
                    'product_type_one' => array(
                        'name' => 'product_type_one',
                        'model' => 'Product_Type_One',
                    ),
                    'type_two' => array(
                        'name' => 'type_two',
                        'model' => 'Product_Type_Two',
                    ),
                ),
            'relatedIndexers' => array(
                'simple_index' => array(
                    'name' => 'simple_index',
                ),
                'custom_product_index' => array(
                    'name' => 'custom_product_index',
                ),
            ),
        ),
        'customer' => array(
            'name' => 'customer',
            'label' => 'Label_One',
            'behaviorModel' => 'Model_Basic',
            'model' => 'Model_One',
            'types' => array(
                'customer_type_one' => array(
                    'name' => 'customer_type_one',
                    'model' => 'Customer_Type_One',
                ),
                'type_two' => array(
                    'name' => 'type_two',
                    'model' => 'Customer_Type_Two',
                ),
            ),
            'relatedIndexers' => array(
                'simple_index' => array(
                    'name' => 'simple_index',
                ),
                'custom_customer_index' => array(
                    'name' => 'custom_customer_index',
                ),
            ),
        ),
    )
);
