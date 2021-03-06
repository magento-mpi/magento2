<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma\NewRma\Tab\Items\Order;

use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Mtf\Fixture\FixtureInterface;

/**
 * Grid for choose order item(configurable product).
 */
class ConfigurableGrid extends Grid
{
    /**
     * Select order item.
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function selectItem(FixtureInterface $product)
    {
        $this->searchAndSelect(['sku' => $this->prepareSku($product)]);
    }

    /**
     * Prepare configurable product sku.
     *
     * @param ConfigurableProductInjectable $product
     * @return string
     */
    public function prepareSku(ConfigurableProductInjectable $product)
    {
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
