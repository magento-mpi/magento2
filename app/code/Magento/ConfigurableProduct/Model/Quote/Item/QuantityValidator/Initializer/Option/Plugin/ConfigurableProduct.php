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
     * @param array $arguments
     *
     * @return void
     */
    public function beforeInitialize(array $arguments)
    {
        /** @var \Magento\Sales\Model\Quote\Item\Option $option */
        /** @var \Magento\Sales\Model\Quote\Item $quoteItem */
        list($option, $quoteItem) = $arguments;

        /* @var $stockItem \Magento\CatalogInventory\Model\Stock\Item */
        $stockItem = $option->getProduct()->getStockItem();

        if ($quoteItem->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE) {
            $stockItem->setProductName($quoteItem->getName());
        }
    }
} 
