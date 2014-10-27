<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab;

use Magento\Bundle\Test\Fixture\BundleProduct;

/**
 * Rma items tab for bundle product.
 */
class BundleItems extends Items
{
    /**
     * Fill item product in rma items grid.
     *
     * @param array $itemData
     * @return void
     */
    protected function fillItem(array $itemData)
    {
        /** @var BundleProduct $product */
        $product = $itemData['product'];
        $bundleSelections = $product->getBundleSelections();
        $checkoutData = $product->getCheckoutData();
        $checkoutOptions = isset($checkoutData['options']['bundle_options'])
            ? $checkoutData['options']['bundle_options']
            : [];

        unset($itemData['product']);
        foreach ($checkoutOptions as $option) {
            foreach ($bundleSelections['products'] as $optionProducts) {
                foreach ($optionProducts as $productItem) {
                    if (false !== strpos($productItem->getName(), $option['value']['name'])) {
                        $fields = $this->dataMapping($itemData);
                        $itemRow = $this->getItemsGrid()->getItemRow($productItem);
                        $this->_fill($fields, $itemRow);
                    }
                }
            }
        }
    }
}
