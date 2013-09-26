<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
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
        'allow_product_types' => array(
            'type_one' => 'type_one'
        ),
    ),
    'type_two' => array(
        'name' => 'type_two',
        'label' => false,
        'model' => 'Instance_Type',
        'composite' => false,
        'index_priority' => 0,
        'can_use_qty_decimals' => false,
        'is_qty' => false,
        'allowed_selection_types' => array(
            'type_two' => 'type_two'
        )
    )
);
