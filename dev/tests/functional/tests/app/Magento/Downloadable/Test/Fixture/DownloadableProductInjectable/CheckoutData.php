<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Fixture\DownloadableProductInjectable;

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
            'with_two_separately_links' => [
                'options' => [
                    'links' => [
                        [
                            'label' => 'link_1',
                            'value' => 'Yes'
                        ]
                    ],
                    'qty' => 2,
                ],
                'cartItem' => [
                    'price' => 23,
                    'subtotal' => 46
                ]
            ],
        ];
        return isset($presets[$name]) ? $presets[$name] : null;
    }
}
