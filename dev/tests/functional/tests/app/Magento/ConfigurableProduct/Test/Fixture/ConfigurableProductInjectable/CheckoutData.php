<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

/**
 * Class CheckoutData
 * Data for fill product form on frontend
 *
 * Data keys:
 *  - preset (Checkout data verification preset name)
 */
class CheckoutData extends \Magento\Catalog\Test\Fixture\CatalogProductSimple\CheckoutData
{
    /**
     * Get preset array
     *
     * @param $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'configurable_options' => [
                    [
                        'title' => 'attribute_0',
                        'value' => 'option_0',
                    ],
                    [
                        'title' => 'attribute_1',
                        'value' => 'option_0',
                    ]
                ],
                'checkoutItemForm' => [
                    'price' => 101,
                ],
                'qty' => 1
            ],
            'two_options' => [
                'configurable_options' => [
                    [
                        'title' => 'attribute_0',
                        'value' => 'option_0',
                    ]
                ],
                'checkoutItemForm' => [
                    'price' => 101,
                ]
            ],
            'two_new_options' => [
                'configurable_options' => [
                    [
                        'title' => 'attribute_0',
                        'value' => 'option_1',
                    ]
                ],
                'checkoutItemForm' => [
                    'price' => 102,
                ]
            ],
            'two_new_options_with_special_price' =>[
                'configurable_options' => [
                    [
                        'title' => 'attribute_0',
                        'value' => 'option_1',
                    ]
                ],
                'checkoutItemForm' => [
                    'price' => 12,
                ]
            ],
            'two_options_with_assigned_product' => [
                'configurable_options' => [
                    [
                        'title' => 'attribute_0',
                        'value' => 'option_0',
                    ]
                ],
                'checkoutItemForm' => [
                    'price' => 101,
                ]
            ],
        ];
        return isset($presets[$name]) ? $presets[$name] : null;
    }
}
