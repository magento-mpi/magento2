<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Magento\Wishlist\Test\Constraint\AssertProductInCustomerWishlistOnBackendGrid;

/**
 * Class AssertConfigurableProductInCustomerWishlistOnBackendGrid
 * Assert that configurable product is present in grid on customer's wish list tab with configure option and qty
 */
class AssertConfigurableProductInCustomerWishlistOnBackendGrid extends AssertProductInCustomerWishlistOnBackendGrid
{
    /**
     * Prepare options
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareOptions(FixtureInterface $product)
    {
        /** @var ConfigurableProductInjectable $product */
        $productOptions = parent::prepareOptions($product);
        $checkoutData = $product->getCheckoutData()['options'];
        if (!empty($checkoutData['configurable_options'])) {
            $configurableAttributesData = $product->getConfigurableAttributesData()['attributes_data'];
            foreach ($checkoutData['configurable_options'] as $optionData) {
                $attribute = $configurableAttributesData[$optionData['title']];
                $productOptions[] = [
                    'option_name' => $attribute['label'],
                    'value' => $attribute['options'][$optionData['value']]['label']
                ];
            }
        }

        return $productOptions;
    }
}
