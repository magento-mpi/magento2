<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Downloadable\Test\Constraint;

use Magento\Downloadable\Test\Fixture\DownloadableProductInjectable;
use Magento\Wishlist\Test\Constraint\AssertProductInCustomerWishlistOnBackendGrid;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertDownloadableProductInCustomerWishlistOnBackendGrid
 * Assert that downloadable product is present in grid on customer's wish list tab with configure option and qty
 */
class AssertDownloadableProductInCustomerWishlistOnBackendGrid extends AssertProductInCustomerWishlistOnBackendGrid
{
    /**
     * Prepare options
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareOptions(FixtureInterface $product)
    {
        /** @var DownloadableProductInjectable $product */
        $productOptions = parent::prepareOptions($product);
        $checkoutData = $product->getCheckoutData()['options'];
        if (!empty($checkoutData['links'])) {
            $downloadableLinks = $product->getDownloadableLinks();
            foreach ($checkoutData['links'] as $optionData) {
                $linkKey = str_replace('link_', '', $optionData['label']);
                $productOptions[] = [
                    'option_name' => 'Links',
                    'value' => $downloadableLinks['downloadable']['link'][$linkKey]['title'],
                ];
            }
        }

        return $productOptions;
    }
}
