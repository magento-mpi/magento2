<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return [
    'empty_required_field' => [
        'title' => '',
        'type' => 'field',
        'sort_order' => 1,
        'is_require' => 1,
        'metadata' => [
            [
                'price' => 10,
                'price_type' => 'fixed',
                'sku' => 'sku1',
                'custom_attributes' => [
                    [
                        'attribute_code' => 'max_characters',
                        'value' => 10
                    ]
                ]
            ]
        ]
    ],
    'negative_price' => [
        'title' => 'area option',
        'type' => 'area',
        'sort_order' => 2,
        'is_require' => 0,
        'metadata' => [
            [
                'price' => -20,
                'price_type' => 'percent',
                'sku' => 'sku2',
                'custom_attributes' => [
                    [
                        'attribute_code' => 'max_characters',
                        'value' => 20
                    ]
                ]
            ]
        ]

    ],
    'negative_value_of_image_size' => [
        'title' => 'file option',
        'type' => 'file',
        'sort_order' => 3,
        'is_require' => 1,
        'metadata' => [
            [
                'price' => 30,
                'price_type' => 'percent',
                'sku' => 'sku3',
                'custom_attributes' => [
                    [
                        'attribute_code' => 'file_extension',
                        'value' => 'jpg'
                    ],
                    [
                        'attribute_code' => 'image_size_x',
                        'value' => -10
                    ],
                    [
                        'attribute_code' => 'image_size_y',
                        'value' => -20
                    ]
                ]
            ]
        ]
    ],
    'option_with_type_select_without_options' => [
        'title' => 'drop_down option',
        'type' => 'drop_down',
        'sort_order' => 4,
        'is_require' => 1,
        'metadata' => [],
    ],
    'title_is_empty' => [
        'title' => 'radio option',
        'type' => 'radio',
        'sort_order' => 5,
        'is_require' => 1,
        'metadata' => [
            [
                'price' => 10,
                'price_type' => 'fixed',
                'sku' => 'radio option 1 sku',
                'custom_attributes' => [
                    [
                        'attribute_code' => 'title',
                        'value' => ''
                    ],
                    [
                        'attribute_code' => 'sort_order',
                        'value' => 1
                    ]
                ]
            ],
        ],
    ],
    'option_with_non_existing_price_type' => [
        'title' => 'checkbox option',
        'type' => 'checkbox',
        'sort_order' => 6,
        'is_require' => 1,
        'metadata' => [
            [
                'price' => 10,
                'price_type' => 'fixed_one',
                'sku' => 'checkbox option 1 sku',
                'custom_attributes' => [
                    [
                        'attribute_code' => 'title',
                        'value' => 'checkbox option 1'
                    ],
                    [
                        'attribute_code' => 'sort_order',
                        'value' => 1
                    ]
                ]
            ],
        ],
    ],
    'option_with_non_existing_option_type' => [
        'title' => 'multiple option',
        'type' => 'multiple_some_value',
        'sort_order' => 7,
        'is_require' => 1,
        'metadata' => [
            [
                'price' => 10,
                'price_type' => 'fixed',
                'sku' => 'multiple option 1 sku',
                'custom_attributes' => [
                    [
                        'attribute_code' => 'title',
                        'value' => 'multiple option 1'
                    ],
                    [
                        'attribute_code' => 'sort_order',
                        'value' => 1
                    ]
                ]
            ],
        ],
    ],
];
