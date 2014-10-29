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
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

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
