<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Bundle\Test\Fixture\BundleProduct;
use Magento\Wishlist\Test\Constraint\AssertProductInCustomerWishlistOnBackendGrid;

/**
 * Class AssertBundleProductInCustomerWishlistOnBackendGrid
 * Assert that bundle product is present in grid on customer's wish list tab with configure option and qty
 */
class AssertBundleProductInCustomerWishlistOnBackendGrid extends AssertProductInCustomerWishlistOnBackendGrid
{
    /**
     * Prepare options
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareOptions(FixtureInterface $product)
    {
        /** @var BundleProduct $product */
        $productOptions = parent::prepareOptions($product);
        $checkoutData = $product->getCheckoutData()['options'];
        if (!empty($checkoutData['bundle_options'])) {
            foreach ($checkoutData['bundle_options'] as $optionData) {
                $productOptions[] = [
                    'option_name' => $optionData['title'],
                    'value' => $optionData['value']['name']
                ];
            }
        }

        return $productOptions;
    }
}
