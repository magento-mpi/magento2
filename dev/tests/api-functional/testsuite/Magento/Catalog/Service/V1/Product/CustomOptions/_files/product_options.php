<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return [
    [
        'title' => 'test_option_code_1',
        'type' => 'field',
        'is_require' => 1,
        'sort_order' => 1,
        'value' => [
            [
                'price' => 10,
                'price_type' => 'fixed',
                'sku' => 'sku1',
                'customAttributes' => [
                    [
                        'attribute_code' => 'max_characters',
                        'value' => 10
                    ]
                ]
            ]
        ]
    ],
    [
        'title' => 'area option',
        'type' => 'area',
        'is_require' => 0,
        'sort_order' => 2,
        'value' => [
            [
                'price' => 20,
                'price_type' => 'percent',
                'sku' => 'sku2',
                'customAttributes' => [
                    [
                        'attribute_code' => 'max_characters',
                        'value' => 20
                    ]
                ]
            ]
        ]

    ],
    [
        'title' => 'file option',
        'type' => 'file',
        'sort_order' => 3,
        'is_require' => 1,
        'value' => [
            [
                'price' => 30,
                'price_type' => 'percent',
                'sku' => 'sku3',
                'customAttributes' => [
                    [
                        'attribute_code' => 'file_extension',
                        'value' => 'jpg'
                    ],
                    [
                        'attribute_code' => 'image_size_x',
                        'value' => 10
                    ],
                    [
                        'attribute_code' => 'image_size_y',
                        'value' => 20
                    ]
                ]
            ]
        ]
    ],
    [
        'title' => 'drop_down option',
        'type' => 'drop_down',
        'sort_order' => 4,
        'is_require' => 1,
        'value' => [
            [
                'price' => 10,
                'price_type' => 'fixed',
                'sku' => 'drop_down option 1 sku',
                'customAttributes' => [
                    [
                        'attribute_code' => 'title',
                        'value' => 'drop_down option 1'
                    ],
                    [
                        'attribute_code' => 'sort_order',
                        'value' => 1
                    ]
                ]
            ],
            [
                'price' => 20,
                'price_type' => 'fixed',
                'sku' => 'drop_down option 2 sku',
                'customAttributes' => [
                    [
                        'attribute_code' => 'title',
                        'value' => 'drop_down option 2'
                    ],
                    [
                        'attribute_code' => 'sort_order',
                        'value' => 2
                    ]
                ]
            ],
        ],
    ],
    [
        'title' => 'radio option',
        'type' => 'radio',
        'sort_order' => 5,
        'is_require' => 1,
        'value' => [
            [
                'price' => 10,
                'price_type' => 'fixed',
                'sku' => 'radio option 1 sku',
                'customAttributes' => [
                    [
                        'attribute_code' => 'title',
                        'value' => 'radio option 1'
                    ],
                    [
                        'attribute_code' => 'sort_order',
                        'value' => 1
                    ]
                ]
            ],
            [
                'price' => 20,
                'price_type' => 'fixed',
                'sku' => 'radio option 2 sku',
                'customAttributes' => [
                    [
                        'attribute_code' => 'title',
                        'value' => 'radio option 2'
                    ],
                    [
                        'attribute_code' => 'sort_order',
                        'value' => 2
                    ]
                ]
            ],
        ],
    ],
    [
        'title' => 'checkbox option',
        'type' => 'checkbox',
        'sort_order' => 6,
        'is_require' => 1,
        'value' => [
            [
                'price' => 10,
                'price_type' => 'fixed',
                'sku' => 'checkbox option 1 sku',
                'customAttributes' => [
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
            [
                'price' => 20,
                'price_type' => 'fixed',
                'sku' => 'checkbox option 2 sku',
                'customAttributes' => [
                    [
                        'attribute_code' => 'title',
                        'value' => 'checkbox option 2'
                    ],
                    [
                        'attribute_code' => 'sort_order',
                        'value' => 2
                    ]
                ]
            ],
        ],
    ],
    [
        'title' => 'multiple option',
        'type' => 'multiple',
        'sort_order' => 7,
        'is_require' => 1,
        'value' => [
            [
                'price' => 10,
                'price_type' => 'fixed',
                'sku' => 'multiple option 1 sku',
                'customAttributes' => [
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
            [
                'price' => 20,
                'price_type' => 'fixed',
                'sku' => 'multiple option 2 sku',
                'customAttributes' => [
                    [
                        'attribute_code' => 'title',
                        'value' => 'multiple option 2'
                    ],
                    [
                        'attribute_code' => 'sort_order',
                        'value' => 2
                    ]
                ]
            ],
        ],
    ],
    [
        'title' => 'date option',
        'type' => 'date',
        'is_require' => 1,
        'sort_order' => 8,
        'value' => [
            [
                'price' => 80.0,
                'price_type' => 'fixed',
                'sku' => 'date option sku',
            ]
        ]
    ],
    [
        'title' => 'date_time option',
        'type' => 'date_time',
        'is_require' => 1,
        'sort_order' => 9,
        'value' => [
            [
                'price' => 90.0,
                'price_type' => 'fixed',
                'sku' => 'date_time option sku',
            ]
        ]
    ],
    [
        'title' => 'time option',
        'type' => 'time',
        'is_require' => 1,
        'sort_order' => 10,
        'value' => [
            [
                'price' => 100.0,
                'price_type' => 'fixed',
                'sku' => 'time option sku',
            ]
        ]
    ],
];
