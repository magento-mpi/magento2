<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Fixture\CatalogProductBundle;

/**
 * Class CheckoutData
 * Data keys:
 *  - preset (Checkout data verification preset name)
 */
class CheckoutData extends \Magento\Catalog\Test\Fixture\CatalogProductSimple\CheckoutData
{
    /**
     * Get preset array
     *
     * @param string $name
     * @return array|null
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                ],
            ],
            'with_not_required_options' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                    [
                        'title' => 'Radio Button Option',
                        'type' => 'Radio Buttons',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                ],
            ],
            'with_custom_options_1' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                ],
                'custom_options' => [
                    [
                        'title' => 0,
                        'value' => 0,
                    ],
                    [
                        'title' => 1,
                        'value' => 0,
                    ],
                    [
                        'title' => 2,
                        'value' => 'Field',
                    ],
                    [
                        'title' => 3,
                        'value' => 'Field',
                    ],
                    [
                        'title' => 4,
                        'value' => 'Area',
                    ],
                    [
                        'title' => 6,
                        'value' => 0,
                    ],
                    [
                        'title' => 7,
                        'value' => 0,
                    ],
                    [
                        'title' => 8,
                        'value' => 0,
                    ],
                    [
                        'title' => 9,
                        'value' => 0,
                    ],
                    [
                        'title' => 10,
                        'value' => '12/12/2014',
                    ],
                    [
                        'title' => 11,
                        'value' => '12/12/2014/12/30/AM',
                    ],
                    [
                        'title' => 12,
                        'value' => '12/12/AM',
                    ],
                ]
            ],
            'with_custom_options_2' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                ],
                'custom_options' => [
                    [
                        'title' => 0,
                        'value' => 0,
                    ],
                ]
            ],
            'all_types_bundle_fixed_and_custom_options' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                    [
                        'title' => 'Radio Button Option',
                        'type' => 'Radio Buttons',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                    [
                        'title' => 'Checkbox Option',
                        'type' => 'Checkbox',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                    [
                        'title' => 'Multiple Select Option',
                        'type' => 'Multiple',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                ],
                'custom_options' => [
                    [
                        'title' => 0,
                        'value' => 'Field',
                    ],
                    [
                        'title' => 1,
                        'value' => 'Area',
                    ],
                    [
                        'title' => 3,
                        'value' => 0,
                    ],
                    [
                        'title' => 4,
                        'value' => 0,
                    ],
                    [
                        'title' => 5,
                        'value' => 0,
                    ],
                    [
                        'title' => 6,
                        'value' => 0,
                    ],
                    [
                        'title' => 7,
                        'value' => '12/12/2014',
                    ],
                    [
                        'title' => 8,
                        'value' => '12/12/2014/12/30/AM',
                    ],
                    [
                        'title' => 9,
                        'value' => '12/12/AM',
                    ],
                ]
            ],
            'all_types_bundle_options' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                    [
                        'title' => 'Radio Button Option',
                        'type' => 'Radio Buttons',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                    [
                        'title' => 'Checkbox Option',
                        'type' => 'Checkbox',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                    [
                        'title' => 'Multiple Select Option',
                        'type' => 'Multiple',
                        'value' => [
                            'name' => '100_dollar_product'
                        ]
                    ],
                ],
            ],
        ];
        return isset($presets[$name]) ? $presets[$name] : null;
    }
}
