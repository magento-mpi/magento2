<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Constraint;

use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Magento\Rma\Test\Fixture\Rma;
use Mtf\Fixture\FixtureInterface;

/**
 * Assert that displayed rma data on edit page equals passed from fixture.
 */
class AssertRmaConfigurableForm extends AssertRmaForm
{
    /**
     * Return item sku.
     *
     * @param FixtureInterface $product
     * @return string
     */
    protected function getItemSku(FixtureInterface $product)
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
