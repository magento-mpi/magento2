<?php
/**
 * Stock item initializer for configurable product type
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model\Quote\Item\QuantityValidator\Initializer\Option\Plugin;

class ConfigurableProduct
{
    /**
     * Initialize stock item for configurable product type
     *
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option $subject
     * @param \Magento\Sales\Model\Quote\Item\Option $option
     * @param \Magento\Sales\Model\Quote\Item $quoteItem
     * @param \Magento\CatalogInventory\Model\Stock\Item $stockItem
     *
     * @return \Magento\CatalogInventory\Model\Stock\Item
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetStockItem(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option $subject,
        \Magento\Sales\Model\Quote\Item\Option $option,
        \Magento\Sales\Model\Quote\Item $quoteItem,
        $stockItem
    ) {
        if ($quoteItem->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $stockItem->setProductName($quoteItem->getName());
        }
        return $stockItem;
    }
}
