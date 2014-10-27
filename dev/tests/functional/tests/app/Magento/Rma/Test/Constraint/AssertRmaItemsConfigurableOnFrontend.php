<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

/**
 * Assert customer can vew return request on Frontend and verify.
 */
class AssertRmaItemsConfigurableOnFrontend extends AssertRmaItemsOnFrontend
{
    /**
     * Prepare product sku.
     *
     * @param FixtureInterface $product
     * @return string
     */
    public function prepareProductSku(FixtureInterface $product)
    {
        /** @var ConfigurableProductInjectable $product */
        $checkoutData = $product->getCheckoutData();
        $checkoutOptions = isset($checkoutData['options']['configurable_options'])
            ? $checkoutData['options']['configurable_options']
            : [];
        $configurableAttributesData = $product->getConfigurableAttributesData();
        $matrixKey = [];

        foreach ($checkoutOptions as $checkoutOption) {
            $matrixKey[] = $checkoutOption['title'] . ':' . $checkoutOption['value'];
        }
        $matrixKey = implode(' ', $matrixKey);

        return $configurableAttributesData['matrix'][$matrixKey]['sku'];
    }
}
