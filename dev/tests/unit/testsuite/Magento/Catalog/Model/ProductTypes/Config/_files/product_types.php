<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'types' => array(
        'type_one' => array(
            'name' => 'type_one',
            'label' => 'Label One',
            'model' => 'Instance_Type',
            'composite' => true,
            'index_priority' => 40,
            'can_use_qty_decimals' => true,
            'is_qty' => true,
            'price_model' => 'Instance_Type_One',
            'price_indexer' => 'Instance_Type_Two',
            'stock_indexer' => 'Instance_Type_Three',
        ),
        'type_two' => array(
            'name' => 'type_two',
            'label' => false,
            'model' => 'Instance_Type',
            'composite' => false,
            'index_priority' => 0,
            'can_use_qty_decimals' => true,
            'is_qty' => false,
            'allowed_selection_types' => array(
                'type_two' => 'type_two'
            ),
            'custom_attributes' => array(
                'some_name' => 'some_value'
            ),
        ),
        'type_three' => array(
            'name' => 'type_three',
            'label' => 'Label Three',
            'model' => 'Instance_Type',
            'composite' => false,
            'index_priority' => 20,
            'can_use_qty_decimals' => false,
            'is_qty' => false,
            'price_model' => 'Instance_Type_Three',
            'price_indexer' => 'Instance_Type_Three',
            'stock_indexer' => 'Instance_Type_Three',
        )
    ),
    'composableTypes' => array('type_one' => 'type_one', 'type_three' => 'type_three'),
);
