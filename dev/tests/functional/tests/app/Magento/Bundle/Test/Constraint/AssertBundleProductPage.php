<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductPage;

/**
 * Class AssertBundleProductPage
 */
class AssertBundleProductPage extends AssertProductPage
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Error messages
     *
     * @var array
     */
    protected $errorsMessages = [
        'name' => '- product name on product view page is not correct.',
        'sku' => '- product sku on product view page is not correct.',
        'price_from' => '- bundle product price from on product view page is not correct.',
        'price_to' => '- bundle product price to on product view page is not correct.',
        'short_description' => '- product short description on product view page is not correct.',
        'description' => '- product description on product view page is not correct.'
    ];

    /**
     * Prepare Price data
     *
     * @param array $price
     * @return array
     */
    protected function preparePrice($price)
    {
        $priceData = $this->product->getDataFieldConfig('price')['source']->getPreset();
        if ($this->product->getPriceView() == 'Price Range') {
            return [
                ['price_from' => $price['price_from'], 'price_to' => $price['price_to']],
                [
                    'price_from' => number_format($priceData['price_from'], 2),
                    'price_to' => number_format($priceData['price_to'], 2)
                ]
            ];
        } else {
            return [
                ['price_from' => $price['price_regular_price']],
                [
                    'price_from' => is_numeric($priceData['price_from'])
                        ? number_format($priceData['price_from'], 2)
                        : $priceData['price_from']
                ]
            ];
        }
    }
}
