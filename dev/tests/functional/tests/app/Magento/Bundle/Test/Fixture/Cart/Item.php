<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Fixture\Cart;

use Mtf\Fixture\FixtureInterface;
use Magento\Bundle\Test\Fixture\BundleProduct;

/**
 * Class Item
 * Data for verify cart item block on checkout page
 *
 * Data keys:
 *  - product (fixture data for verify)
 */
class Item extends \Magento\Catalog\Test\Fixture\Cart\Item
{
    /**
     * @constructor
     * @param FixtureInterface $product
     */
    public function __construct(FixtureInterface $product)
    {
        parent::__construct($product);

        /** @var BundleProduct $product */
        $bundleSelection = $product->getBundleSelections();
        $checkoutData = $product->getCheckoutData();
        $checkoutBundleOptions = isset($checkoutData['options']['bundle_options'])
            ? $checkoutData['options']['bundle_options']
            : [];

        foreach ($checkoutBundleOptions as $checkoutOptionKey => $checkoutOption) {
            // Find option and value keys
            $attributeKey = null;
            $optionKey = null;
            foreach ($bundleSelection['bundle_options'] as $key => $option) {
                if ($option['title'] == $checkoutOption['title']) {
                    $attributeKey = $key;

                    foreach ($option['assigned_products'] as $valueKey => $value) {
                        if (false !== strpos($value['search_data']['name'], $checkoutOption['value']['name'])) {
                            $optionKey = $valueKey;
                        }
                    }
                }
            }

            // Prepare option data
            $bundleSelectionAttribute = $bundleSelection['products'][$attributeKey];
            $bundleOptions = $bundleSelection['bundle_options'][$attributeKey];
            $value = $bundleSelectionAttribute[$optionKey]->getName();
            $qty = $bundleOptions['assigned_products'][$optionKey]['data']['selection_qty'];
            $price = $product->getPriceType() == 'Dynamic'
            ? number_format($bundleSelectionAttribute[$optionKey]->getPrice(), 2)
            : number_format($bundleOptions['assigned_products'][$optionKey]['data']['selection_price_value'], 2);
            $optionData = [
                'title' => $checkoutOption['title'],
                'value' => "{$qty} x {$value} {$price}"
            ];

            $checkoutBundleOptions[$checkoutOptionKey] = $optionData;
        }

        $this->data['options'] += $checkoutBundleOptions;
    }

    /**
     * Persist fixture
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param string $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        //
    }
}
