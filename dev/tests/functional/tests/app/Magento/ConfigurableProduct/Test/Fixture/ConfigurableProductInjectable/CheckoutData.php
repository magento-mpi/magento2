<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

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
            'two_options' => [
                'configurable_options' => [
                    [
                        'title' => 'attribute_0',
                        'type' => 'dropdown',
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
                        'type' => 'dropdown',
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
                        'type' => 'dropdown',
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
                        'type' => 'dropdown',
                        'value' => 'option_0',
                    ]
                ],
                'checkoutItemForm' => [
                    'price' => 101,
                ]
            ],
        ];

        if (!isset($presets[$this->currentPreset])) {
            return null;
        }
        return $presets[$this->currentPreset];
    }
}
