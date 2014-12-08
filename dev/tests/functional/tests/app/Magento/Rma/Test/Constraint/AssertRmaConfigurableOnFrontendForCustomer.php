<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Magento\Rma\Test\Fixture\Rma;
use Mtf\Fixture\FixtureInterface;

/**
 * Assert that rma with item as configurable product is correct display on frontend (MyAccount - My Returns).
 */
class AssertRmaConfigurableOnFrontendForCustomer extends AssertRmaOnFrontendForCustomer
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
