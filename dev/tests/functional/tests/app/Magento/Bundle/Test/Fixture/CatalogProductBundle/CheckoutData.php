<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Fixture\CatalogProductBundle;

use Magento\Catalog\Test\Fixture\CatalogProductSimple\CheckoutData as AbstractCheckoutData;

/**
 * Class CheckoutData
 * Data keys:
 *  - preset (Checkout data verification preset name)
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class CheckoutData extends AbstractCheckoutData
{
    /**
     * Get preset array
     *
     * @return array|null
     */
    public function getPreset()
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
                        'option' => 1,
                        'value' => [1],
                    ],
                    [
                        'option' => 2,
                        'value' => [1],
                    ],
                    [
                        'option' => 3,
                        'value' => ['Field'],
                    ],
                    [
                        'option' => 4,
                        'value' => ['Field'],
                    ],
                    [
                        'option' => 5,
                        'value' => ['Area'],
                    ],
                    [
                        'option' => 7,
                        'value' => [1],
                    ],
                    [
                        'option' => 8,
                        'value' => [1],
                    ],
                    [
                        'option' => 9,
                        'value' => [1],
                    ],
                    [
                        'option' => 10,
                        'value' => [1],
                    ],
                    [
                        'option' => 11,
                        'value' => ['12/12/2014'],
                    ],
                    [
                        'option' => 12,
                        'value' => ['12/12/2014/12/30/AM'],
                    ],
                    [
                        'option' => 13,
                        'value' => ['12/12/AM'],
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
                        'option' => 1,
                        'value' => [1],
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
                        'option' => 1,
                        'value' => ['Field'],
                    ],
                    [
                        'option' => 2,
                        'value' => ['Area'],
                    ],
                    [
                        'option' => 4,
                        'value' => [1],
                    ],
                    [
                        'option' => 5,
                        'value' => [1],
                    ],
                    [
                        'option' => 6,
                        'value' => [1],
                    ],
                    [
                        'option' => 7,
                        'value' => [1],
                    ],
                    [
                        'option' => 8,
                        'value' => ['12/12/2014'],
                    ],
                    [
                        'option' => 9,
                        'value' => ['12/12/2014/12/30/AM'],
                    ],
                    [
                        'option' => 10,
                        'value' => ['12/12/AM'],
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
        if (!isset($presets[$this->currentPreset])) {
            return null;
        }
        return $presets[$this->currentPreset];
    }
}
