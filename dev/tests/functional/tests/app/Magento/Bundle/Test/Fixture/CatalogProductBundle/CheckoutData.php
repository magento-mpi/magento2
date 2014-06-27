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
 */
class CheckoutData extends AbstractCheckoutData
{
    /**
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
                        'type' => 'Drop-down',
                        'title' => 'custom option drop down',
                        'value' => ['30 bucks'],
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
                        'type' => 'Drop-down',
                        'title' => 'custom option drop down',
                        'value' => ['10 percent'],
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
                        'type' => 'Field',
                        'title' => 'custom option Field',
                        'value' => ['Field'],
                    ],
                    [
                        'type' => 'Area',
                        'title' => 'custom option Area',
                        'value' => ['Area'],
                    ],
                    [
                        'type' => 'Radio Buttons',
                        'title' => 'custom option Radio Buttons',
                        'value' => ['20 percent'],
                    ],
                    [
                        'type' => 'Drop-down',
                        'title' => 'custom option drop down',
                        'value' => ['20 percent'],
                    ],
                    [
                        'type' => 'Checkbox',
                        'title' => 'custom option Checkbox',
                        'value' => ['20 percent'],
                    ],
                    [
                        'type' => 'Multiple Select',
                        'title' => 'custom option Multiple Select',
                        'value' => ['20 percent'],
                    ],
                    [
                        'type' => 'Date',
                        'title' => 'custom option Date',
                        'value' => ['12/12/2014'],
                    ],
                    [
                        'type' => 'Date & Time',
                        'title' => 'custom option Date & Time',
                        'value' => ['12/12/2014/12/30/AM'],
                    ],
                    [
                        'type' => 'Time',
                        'title' => 'custom option Time',
                        'value' => ['12/12/2014/12/30/AM'],
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
